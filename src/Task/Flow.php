<?php

namespace Be\App\Etl\Task;

use Be\App\ServiceException;
use Be\Be;
use Be\Task\Task;
use Be\Task\TaskException;

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

            try {

                $sql = 'SELECT * FROM etl_flow_node WHERE flow_id = ? ORDER BY index ASC';
                $flowNodes = $db->getObjects($sql, [$flow->id]);
                if (count($flowNodes) === 0) {
                    throw new ServiceException('未配置有效处理节点，任务中止！');
                }

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
                }


                // 开始和䁣
                foreach ($flowNodes as $flowNode) {
                    $serviceFlowNodeItem = $itemServices[$flowNode->id];
                    $serviceFlowNodeItem->start($flowNode);
                }


                $flowNode = $flowNodes[0];
                if ($flowNode->type !== 'input') {
                    throw new ServiceException('首个节点非办理入节点，任务中止！');
                }

                $serviceFlowNodeItem = $itemServices[$flowNode->id];
                $inputs = $serviceFlowNodeItem->process($flowNode);

                foreach ($inputs as $input) {
                    $i = 0;
                    $lastOutput = $input;
                    foreach ($flowNodes as $flowNode) {

                        if ($i === 0) {
                            continue;
                        }

                        $serviceFlowNodeItem = $serviceFlow->getNodeItemService($flowNode->item_type);

                        $output = $serviceFlowNodeItem->process($flowNode, $lastOutput);

                        $lastOutput = $output;

                        $i++;
                    }
                }

                // 处理完成
                foreach ($flowNodes as $flowNode) {
                    $serviceFlowNodeItem = $itemServices[$flowNode->id];
                    $serviceFlowNodeItem->finish($flowNode);
                }

            } catch (\Throwable $t) {

            }


        }

    }

}
