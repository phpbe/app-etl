<?php

namespace Be\App\Etl\Task;

use Be\App\ServiceException;
use Be\Be;
use Be\Task\Task;

/**
 * 加工
 *
 * @BeTask("数据流处理")
 */
class Flow extends Task
{
    /**
     * 执行超时时间
     *
     * @var null|int
     */
    protected $timeout = 300;


    public function execute()
    {
        $db = Be::getDb();
        $sql = 'SELECT * FROM etl_flow WHERE is_enable = 1 AND is_delete = 0';
        $flows = $db->getObjects($sql);
        if (count($flows) === 0) return;

        $serviceFlow = Be::getService('App.Etl.Admin.Flow');

        foreach ($flows as $flow) {

            $sql = 'SELECT * FROM etl_flow_node WHERE flow_id = ? ORDER BY `index` ASC';
            $flowNodes = $db->getObjects($sql, [$flow->id]);
            if (count($flowNodes) === 0) {
                continue;
            }

            $flowLog = new \stdClass();
            $flowLog->id = $db->uuid();
            $flowLog->flow_id = $flow->id;
            $flowLog->status = 'create';
            $flowLog->message = '';
            $flowLog->finish_time = '1970-01-02 00:00:00';
            $flowLog->total = 0;
            $flowLog->total_success = 0;
            $flowLog->create_time = date('Y-m-d H:i:s');
            $flowLog->update_time = date('Y-m-d H:i:s');
            $db->insert('etl_flow_log', $flowLog);

            try {

                $flowNodeLogs = [];

                $itemServices = [];
                foreach ($flowNodes as $flowNode) {
                    $sql = 'SELECT * FROM etl_flow_node_' . $flowNode->item_type . ' WHERE id = ?';
                    $flowNodeItem = $db->getObject($sql, [$flowNode->item_id]);
                    if (!$flowNodeItem) {
                        throw new ServiceException('数据流 子项（# ' . $flowNode->item_id . '）不存在！');
                    }
                    $flowNode->item = $flowNodeItem;

                    $arr = explode('_', $flowNode->item_type);
                    $serviceName = '\\Be\\App\\Etl\\Service\\Admin\\FlowNode\\' . ucfirst($arr[0]) . '\\' . ucfirst($arr[1]);
                    $itemServices[$flowNode->id] = new $serviceName();

                    $flowNodeLog = new \stdClass();
                    $flowNodeLog->id = $db->uuid();
                    $flowNodeLog->flow_log_id = $flowLog->id;
                    $flowNodeLog->flow_node_id = $flowNode->id;
                    $flowNodeLog->index = $flowNode->index;
                    $flowNodeLog->config = serialize($flowNode);
                    $flowNodeLog->output_file = '';
                    $flowNodeLog->total_success = 0;
                    $flowNodeLog->create_time = date('Y-m-d H:i:s');
                    $flowNodeLog->update_time = date('Y-m-d H:i:s');
                    $db->insert('etl_flow_node_log', $flowNodeLog);

                    $flowNodeLogs[$flowNode->id] = $flowNodeLog;
                }

                // 开始和䁣
                foreach ($flowNodes as $flowNode) {
                    $serviceFlowNodeItem = $itemServices[$flowNode->id];
                    $flowNodeLog = $flowNodeLogs[$flowNode->id];
                    $serviceFlowNodeItem->start($flowNode, $flowLog, $flowNodeLog);
                }

                $flowNode = $flowNodes[0];
                if ($flowNode->type !== 'input') {
                    throw new ServiceException('首个节点非办理入节点，任务中止！');
                }

                $serviceFlowNodeItem = $itemServices[$flowNode->id];

                // 输入的总数据数
                $total = $serviceFlowNodeItem->getTotal($flowNode);
                $flowLog->total = $total;

                // 状态标记为运行中
                $flowLog->status = 'running';
                $flowLog->update_time = date('Y-m-d H:i:s');
                $db->update('etl_flow_log', $flowLog);

                $flowNodeLog = $flowNodeLogs[$flowNode->id];
                $inputs = $serviceFlowNodeItem->process($flowNode, $flowLog, $flowNodeLog);

                foreach ($inputs as $input) {

                    $i = 0;
                    $lastOutput = $input;
                    foreach ($flowNodes as $flowNode) {

                        if ($i === 0) {
                            $i++;
                            continue;
                        }

                        $serviceFlowNodeItem = $itemServices[$flowNode->id];
                        $flowNodeLog = $flowNodeLogs[$flowNode->id];

                        try {
                            $output = $serviceFlowNodeItem->process($flowNode, $lastOutput, $flowLog, $flowNodeLog);

                            $flowNodeItemLog = new \stdClass();
                            $flowNodeItemLog->id = $db->uuid();
                            $flowNodeItemLog->flow_node_log_id = $flowNodeLog->id;
                            $flowNodeItemLog->input = serialize($lastOutput);
                            $flowNodeItemLog->output = serialize($output);
                            $flowNodeItemLog->success = 1;
                            $flowNodeItemLog->message = '';
                            $flowNodeItemLog->create_time = date('Y-m-d H:i:s');
                            $db->insert('etl_flow_node_item_log', $flowNodeItemLog);

                        } catch (\Throwable $t) {

                            $flowNodeItemLog = new \stdClass();
                            $flowNodeItemLog->id = $db->uuid();
                            $flowNodeItemLog->flow_node_log_id = $flowNodeLog->id;
                            $flowNodeItemLog->input = serialize($lastOutput);
                            $flowNodeItemLog->output = '';
                            $flowNodeItemLog->success = 0;
                            $flowNodeItemLog->message = $t->getMessage();
                            $flowNodeItemLog->create_time = date('Y-m-d H:i:s');
                            $db->insert('etl_flow_node_item_log', $flowNodeItemLog);

                            break;
                        }

                        $flowNodeLog->total_success++;
                        if ($flowNodeLog->total_success % 100 === 0) {
                            $flowNodeLog->update_time = date('Y-m-d H:i:s');
                            $db->update('etl_flow_node_log', $flowNodeLog);
                        }

                        $lastOutput = $output;
                    }

                    $flowLog->total_success++;
                    if ($flowLog->total_success % 100 === 0) {
                        $flowLog->update_time = date('Y-m-d H:i:s');
                        $db->update('etl_flow_log', $flowLog);
                    }
                }

                // 处理完成
                foreach ($flowNodes as $flowNode) {
                    $serviceFlowNodeItem = $itemServices[$flowNode->id];
                    $flowNodeLog = $flowNodeLogs[$flowNode->id];
                    $serviceFlowNodeItem->finish($flowNode, $flowLog, $flowNodeLog);

                    $flowNodeLog->update_time = date('Y-m-d H:i:s');
                    $db->update('etl_flow_node_log', $flowNodeLog);
                }

                $flowLog->status = 'finish';
                $flowLog->finish_time = date('Y-m-d H:i:s');
                $flowLog->update_time = date('Y-m-d H:i:s');
                $db->update('etl_flow_log', $flowLog);

            } catch (\Throwable $t) {
                $flowLog->status = 'error';
                $flowLog->message = $t->getMessage();
                $flowLog->update_time = date('Y-m-d H:i:s');
                $db->update('etl_flow_log', $flowLog);
            }
        }

    }

}
