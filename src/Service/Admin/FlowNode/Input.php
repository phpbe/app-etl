<?php

namespace Be\App\Etl\Service\Admin\FlowNode;


use Be\Be;

abstract class Input
{

    /**
     * 编辑数据流
     *
     * @param array $formDataNode 表单数据
     * @return array
     * @throws \Throwable
     */
    abstract public function test(array $formDataNode): object;

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
     */
    public function start(object $flowNode)
    {
    }


    /**
     * 计划任务处理数据
     *
     * @param object $flowNode 数据流节点
     * @return \Generator 输出
     * @throws \Throwable
     */
    abstract public function process(object $flowNode): \Generator;

    /**
     * 处理完成
     *
     * @param object $flowNode 数据流节点
     */
    public function finish(object $flowNode)
    {
    }

}
