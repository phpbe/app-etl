<?php

namespace Be\App\Etl\Service\Admin\FlowNode;


abstract class FlowNode
{

    /**
     * 插入数据库
     * @param string $flowNodeId
     * @param array $formDataNode
     * @return object
     */
    abstract public function insert(string $flowNodeId, array $formDataNode): object;

    /**
     * 格式化数据库中读取出来的数据
     *
     * @param object $nodeItem
     * @return object
     */
    abstract public function format(object $nodeItem): object;

    /**
     * 开如处理处理
     *
     * @param object $flowNode 数据流节点
     * @param object $flowLog 流据流日志
     * @param object $flowNodeLog 流据流节点日志
     */
    public function start(object $flowNode, object $flowLog, object $flowNodeLog)
    {
    }

    /**
     * 处理完成
     *
     * @param object $flowNode 数据流节点
     * @param object $flowLog 流据流日志
     * @param object $flowNodeLog 流据流节点日志
     */
    public function finish(object $flowNode, object $flowLog, object $flowNodeLog)
    {
    }

}
