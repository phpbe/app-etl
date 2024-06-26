<?php

namespace Be\App\Etl\Service\Admin\FlowNode;


abstract class Output extends FlowNode
{

    /**
     * 获取类型
     *
     * @return string
     */
    public function getItemTypeName(): string {
        return '输出';
    }

    /**
     * 编辑数据流
     *
     * @param array $formDataNode 表单数据
     * @param object $input 输入数据
     * @return object
     * @throws \Throwable
     */
    abstract public function test(array $formDataNode, object $input): object;

    /**
     * 计划任务处理数据
     *
     * @param object $flowNode 数据流节点
     * @param object $input 输入
     * @param object $flowLog 流据流日志
     * @param object $flowNodeLog 流据流节点日志
     * @return object $output 输出
     *   $output->_success = 1|0 是否成功
     *   $output->_message = '消息'
     * @throws \Throwable
     */
    abstract public function process(object $flowNode, object $input, object $flowLog, object $flowNodeLog): object;

}
