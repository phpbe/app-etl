<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Input;


use Be\App\Etl\Service\Admin\FlowNode\Input;
use Be\Be;

class Csv extends Input
{

    public function getItemName(): string
    {
        return 'CSV';
    }

    public function test(array $formDataNode): object
    {
        return new \stdClass();
    }


    public function edit(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_input_csv');

        if (isset($formDataNode['item']['id']) && is_string($formDataNode['item']['id']) && strlen($formDataNode['item']['id']) === 36) {
            try {
                $tupleFlowNodeItem->load($formDataNode['item']['id']);
            } catch (\Throwable $t) {
            }
        }

        $tupleFlowNodeItem->flow_node_id = $flowNodeId;

        $tupleFlowNodeItem->update_time = date('Y-m-d H:i:s');

        if ($tupleFlowNodeItem->isLoaded()) {
            $tupleFlowNodeItem->update();
        } else {
            $tupleFlowNodeItem->create_time = date('Y-m-d H:i:s');
            $tupleFlowNodeItem->insert();
        }

        return $tupleFlowNodeItem->toObject();
    }

    /**
     * 格式化数据库中读取出来的数据
     *
     * @param object $nodeItem
     * @return object
     */
    public function format(object $nodeItem): object
    {
        $nodeItem->output = unserialize($nodeItem->output);
        if ($nodeItem->output === '') {
            $nodeItem->output = false;
        }

        return $nodeItem;
    }


    public function process(object $flowNode, object $flowLog, object $flowNodeLog): \Generator
    {

    }

    /**
     * 获取总数据数
     * @param object $flowNode
     * @return int
     */
    public function getTotal(object $flowNode): int
    {
        // TODO: Implement getTotal() method.
    }
}
