<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Process;


use Be\App\Etl\Service\Admin\FlowNode\Process;
use Be\App\ServiceException;
use Be\Be;

class Code extends Process
{

    public function getItemName(): string
    {
        return '代理';
    }

    /**
     * 编辑数据流
     *
     * @param array $formDataNode 表单数据
     * @param object $input 输入数据
     * @return array 
     * @throws \Throwable
     */
    public function test(array $formDataNode, object $input): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['code']) || !is_string($formDataNode['item']['code']) || $formDataNode['item']['code'] === '') {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 代码（code）参数无效！');
        }

        try {
            $fn = eval('return function(object $input): object {' . $formDataNode['item']['code'] . '};');
            $output = $fn($input);
        } catch (\Throwable $t) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 代码（code）执行出错：' . $t->getMessage());
        }

        return $output;
    }


    /**
     * 插入数据库
     * @param string $flowNodeId
     * @param array $formDataNode
     * @return object
     */
    public function insert(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_process_code');
        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->code = $formDataNode['item']['code'];
        $tupleFlowNodeItem->output = serialize($formDataNode['item']['output']);
        $tupleFlowNodeItem->create_time = date('Y-m-d H:i:s');
        $tupleFlowNodeItem->update_time = date('Y-m-d H:i:s');
        $tupleFlowNodeItem->insert();
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

    /**
     * 计划任务处理数据
     *
     * @param object $flowNode 数据流节点
     * @param object $input 输入
     * @return object 输出
     * @throws \Throwable
     */
    public function process(object $flowNode, object $input, object $flowLog, object $flowNodeLog): object
    {
        try {
            $fn = eval('return function(object $input): object {' . $flowNode->item->code . '};');
            $output = $fn($input);
        } catch (\Throwable $t) {
            throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 代码（code）执行出错：' . $t->getMessage());
        }

        return $output;
    }

}
