<?php

namespace Be\App\Etl\Service\Admin\FlowNode;


use Be\Be;

abstract class Input extends FlowNode
{

    /**
     * 获取类型
     *
     * @return string
     */
    public function getItemTypeName(): string {
        return '输入';
    }

    /**
     * 编辑数据流
     *
     * @param array $formDataNode 表单数据
     * @return array
     * @throws \Throwable
     */
    abstract public function test(array $formDataNode): object;


    /**
     * 获取总数据数
     * @param object $flowNode
     * @return int
     */
    abstract public function getTotal(object $flowNode): int;


    /**
     * 计划任务处理数据
     *
     * @param object $flowNode 数据流节点
     * @param object $flowLog 流据流日志
     * @param object $flowNodeLog 流据流节点日志
     * @return \Generator 输出
     * @throws \Throwable
     */
    abstract public function process(object $flowNode, object $flowLog, object $flowNodeLog): \Generator;


}
