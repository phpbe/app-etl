<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Output;


use Be\App\Etl\Service\Admin\FlowNode\Output;

class Api extends Output
{

    /**
     * 编辑数据流
     *
     * @param array $formData 表单数据
     * @param object $input 输入数据
     * @return object
     * @throws \Throwable
     */
    public function test(array $formData, object $input): object
    {
        return new \stdClass();
    }


    public function handle(object $input): array
    {
        // TODO: Implement handle() method.
    }
}
