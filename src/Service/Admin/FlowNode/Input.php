<?php

namespace Be\App\Etl\Service\Admin\FlowNode;


abstract class Input
{

    /**
     * 编辑数据流
     *
     * @param array $formData 表单数据
     * @return array
     * @throws \Throwable
     */
    abstract public function test(array $formData): object;


    /**
     * 计划任务数据
     *
     * @param object $input 输入
     * @return array 输出
     * @throws \Throwable
     */
    abstract public function handle(object $input): array;

}
