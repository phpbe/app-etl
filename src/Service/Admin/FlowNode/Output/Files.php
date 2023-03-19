<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Output;


use Be\App\Etl\Service\Admin\FlowNode\Output;
use Be\App\ServiceException;

class Files extends Output
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

        if (!isset($formData['index']) || !is_numeric($formData['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formData['name']) || !is_string($formData['name']) || !in_array($formData['name'], ['template', 'code'])) {
            throw new ServiceException('节点 ' . $formData['index'] . ' 文件名处理方式（name）参数无效！');
        }

        $output = new \stdClass();

        if ($formData['name'] === 'template') {

            if (!isset($formData['name_template']) || !is_string($formData['name_template']) || strlen($formData['name_template']) === 0) {
                throw new ServiceException('节点 ' . $formData['index'] . ' 文件名称模板（name_template）参数无效！');
            }

            $name = $formData['name_template'];
            foreach ((array)$input as $k => $v) {
                $name = str_replace('{' . $k . '}', $v, $name);
            }
            $output->name = $name;

        } else {

            if (!isset($formData['name_code']) || !is_string($formData['name_code']) || strlen($formData['name_code']) === 0) {
                throw new ServiceException('节点 ' . $formData['index'] . ' 文件名代码处理（name_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): string {' . $formData['name_code'] . '};');
                $output->name = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $formData['index'] . ' 文件名代码处理（name_code）执行出错：' . $t->getMessage());
            }
        }

        if (!isset($formData['content']) || !is_string($formData['content']) || !in_array($formData['content'], ['template', 'code'])) {
            throw new ServiceException('节点 ' . $formData['index'] . ' 内容处理方式（content）参数无效！');
        }

        if ($formData['content'] === 'template') {

            if (!isset($formData['content_template']) || !is_string($formData['content_template']) || strlen($formData['content_template']) === 0) {
                throw new ServiceException('节点 ' . $formData['index'] . ' 文件内容模板（content_template）参数无效！');
            }

            $content = $formData['content_template'];
            foreach ((array)$input as $k => $v) {
                $content = str_replace('{' . $k . '}', $v, $content);
            }
            $output->content = $content;

        } else {

            if (!isset($formData['content_code']) || !is_string($formData['content_code']) || strlen($formData['content_code']) === 0) {
                throw new ServiceException('节点 ' . $formData['index'] . ' 文件内容代码处理（content_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): string {' . $formData['content_code'] . '};');
                $output->content = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $formData['index'] . ' 文件内容代码处理（content_code）执行出错：' . $t->getMessage());
            }
        }

        if (count((array)$output) === 0) {
            throw new ServiceException('节点 ' . $formData['index'] . ' 输出数据为空！');
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
