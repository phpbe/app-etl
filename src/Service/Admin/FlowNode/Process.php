<?php

namespace Be\App\Etl\Service\Admin\FlowNode;


abstract class Process
{

    /**
     * 编辑数据流
     *
     * @param array $formDataNode 表单数据
     * @param object $input 输入数据
     * @return array
     * @throws \Throwable
     */
    abstract public function test(array $formDataNode, object $input): object;

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
     * 计划任务数据
     *
     * @param object $input 输入
     * @return array 输出
     * @throws \Throwable
     */
    abstract public function handle(object $input): array;

}
