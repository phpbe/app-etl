<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Input;


use Be\App\Etl\Service\Admin\FlowNode\Input;
use Be\App\ServiceException;
use Be\Be;
use Be\Db\Driver;
use Be\Util\Time\Datetime;

class Code extends Input
{


    public function getItemName(): string
    {
        return '代码';
    }

    /**
     * 编辑数据流
     *
     * @param array $formDataNode['item'] 表单数据
     * @return array 
     * @throws \Throwable
     */
    public function test(array $formDataNode): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['code']) || !is_string($formDataNode['item']['code']) || $formDataNode['item']['code'] === '') {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 代码（code）参数无效！');
        }

        try {
            $fn = eval('return function(): \Generator {' . $formDataNode['item']['code'] . '};');
            $output = $fn();
        } catch (\Throwable $t) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 代码（code）执行出错：' . $t->getMessage());
        }

        return $output;
    }

    public function edit(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_input_code');

        if (isset($formDataNode['item']['id']) && is_string($formDataNode['item']['id']) && strlen($formDataNode['item']['id']) === 36) {
            try {
                $tupleFlowNodeItem->load($formDataNode['item']['id']);
            } catch (\Throwable $t) {
            }
        }

        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->code = $formDataNode['item']['code'];
        $tupleFlowNodeItem->output = serialize($formDataNode['item']['output']);

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


    public function getTotal(object $flowNode): int
    {
        return 999999;
    }

    public function process(object $flowNode, object $flowLog, object $flowNodeLog): \Generator
    {
        try {
            $fn = eval('return function(): \Generator {' . $flowNode->item->code . '};');
            $output = $fn();
        } catch (\Throwable $t) {
            throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 代码（code）执行出错：' . $t->getMessage());
        }

        return $output;
    }


}
