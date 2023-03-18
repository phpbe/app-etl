<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Process;


use Be\App\Etl\Service\Admin\FlowNode\Process;
use Be\App\ServiceException;

class Code extends Process
{

    /**
     * 编辑数据流
     *
     * @param array $formData 表单数据
     * @param object $input 输入数据
     * @return array 
     * @throws \Throwable
     */
    public function test(array $formData, object $input): object
    {
        if (!isset($formData['index']) || !is_numeric($formData['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formData['code']) || !is_string($formData['code']) || strlen($formData['code']) === 0) {
            throw new ServiceException('节点 ' .$formData['index'] . ' 代码（code）参数无效！');
        }

        try {
            $fn = eval('return function(object $input): object {' . $formData['code'] . '};');
            $output = $fn($input);
        } catch (\Throwable $t) {
            throw new ServiceException('节点 ' .$formData['index'] . ' 代码（code）执行出错：' . $t->getMessage());
        }

        return $output;
    }


    /**
     * 计划任务数据
     *
     * @param object $input 输入
     * @return array 输出
     * @throws \Throwable
     */
    public function handle(object $input): array
    {
        // TODO: Implement handle() method.
    }
}
