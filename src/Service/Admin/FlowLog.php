<?php

namespace Be\App\Etl\Service\Admin;


use Be\App\ServiceException;
use Be\Be;

class FlowLog
{

    /**
     * 获取数据流运行记录
     *
     * @param string $flowLogId
     * @return object
     */
    public function getFlowLog(string $flowLogId): object
    {
        $db = Be::getDb();

        $sql = 'SELECT * FROM etl_flow_log WHERE id = ?';
        $flowLog = $db->getObject($sql, [$flowLogId]);
        if (!$flowLog) {
            throw new ServiceException('数据流运行记录（# ' . $flowLogId . '）不存在！');
        }

        $sql = 'SELECT * FROM etl_flow WHERE id = ?';
        $flow= $db->getObject($sql, [$flowLog->flow_id]);
        if ($flow) {
            $flowLog->flow = $flow;
        }

        $sql = 'SELECT * FROM etl_flow_node_log WHERE flow_log_id = ? ORDER BY `index` ASC';
        $nodeLogs = $db->getObjects($sql, [$flowLogId]);
        foreach ($nodeLogs as $nodeLog) {
            $nodeLog->config = unserialize($nodeLog->config);

            $arr = explode('_', $nodeLog->item_type);
            $serviceName = '\\Be\\App\\Etl\\Service\\Admin\\FlowNode\\' . ucfirst($arr[0]) . '\\' . ucfirst($arr[1]);
            $service = new $serviceName();

            $nodeLog->itemTypeName = $service->getItemTypeName();
            $nodeLog->itemName = $service->getItemName();
        }

        $flowLog->nodeLogs = $nodeLogs;

        return $flowLog;
    }


    /**
     * 删除运行日志
     *
     * @param array $flowLogIds
     * @return void
     */
    public function deleteFlowLogs(array $flowLogIds)
    {
        $db = Be::getDb();

        $sql = 'SELECT * FROM etl_flow_log WHERE id IN (' . implode(',', array_fill(0, count($flowLogIds), '?')) . ')';
        $flowLogs = $db->getObjects($sql, $flowLogIds);
        if (count($flowLogs) === 0) {
            return;
        }

        // 删除相关文件
        foreach ($flowLogs as $flowLog) {
            $sql = 'SELECT * FROM etl_flow_node_log WHERE flow_log_id = ?';
            $flowNodeLogs = $db->getObjects($sql, [$flowLog->id]);
            foreach ($flowNodeLogs as $flowNodeLog) {
                $dir = Be::getRuntime()->getRootPath() . '/data/App/Etl/output_files/' . $flowNodeLog->id;
                if (is_dir($dir)) {
                    \Be\Util\File\Dir::rm($dir);
                }
            }
        }

        // 删除数据库记录
        foreach ($flowLogs as $flowLog) {
            $sql = 'SELECT * FROM etl_flow_node_log WHERE flow_log_id = ?';
            $flowNodeLogs = $db->getObjects($sql, [$flowLog->id]);
            foreach ($flowNodeLogs as $flowNodeLog) {
                $sql = 'DELETE FROM etl_flow_node_item_log WHERE flow_node_log_id = ?';
                $db->query($sql, [$flowNodeLog->id]);
            }

            $sql = 'DELETE FROM etl_flow_node_log WHERE flow_log_id = ?';
            $db->query($sql, [$flowLog->id]);
        }

        $sql = 'DELETE FROM etl_flow_log WHERE id IN (' . implode(',', array_fill(0, count($flowLogIds), '?')) . ')';
        $db->query($sql, $flowLogIds);

    }

}
