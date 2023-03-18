<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Output;


use Be\App\Etl\Service\Admin\FlowNode\Output;
use Be\App\ServiceException;
use Be\Be;

class Csv extends Output
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

        if (!isset($formData['field_mapping']) || !is_string($formData['field_mapping']) || !in_array($formData['field_mapping'], ['mapping', 'code'])) {
            throw new ServiceException('节点 ' . $formData['index'] . ' 字段映射类型（field_mapping）参数无效！');
        }

        if ($formData['field_mapping'] === 'mapping') {

            if (!isset($formData['field_mapping_details']) || !is_array($formData['field_mapping_details'])) {
                throw new ServiceException('节点 ' . $formData['index'] . ' 字段映射（field_mapping_details）参数无效！');
            }

            $output = new \stdClass();

            $i = 1;
            foreach ($formData['field_mapping_details'] as $mapping) {

                if (!isset($mapping['field']) || !is_string($mapping['field']) || strlen($mapping['field']) === 0) {
                    throw new ServiceException('节点 ' . $mapping['index'] . ' 字段映射第 ' . $i . ' 行 列名（field）参数无效！');
                }

                $field = $mapping['field'];

                if (!isset($mapping['type']) || !is_string($mapping['type']) || !in_array($mapping['type'], ['input_field', 'custom'])) {
                    throw new ServiceException('节点 ' . $mapping['index'] . ' 字段映射第 ' . $i . ' 行 取值类型（type）参数无效！');
                }

                if ($mapping['type'] === 'input_field') {

                    if (!isset($mapping['input_field']) || !is_string($mapping['input_field']) || strlen($mapping['input_field']) === 0) {
                        throw new ServiceException('节点 ' . $mapping['index'] . ' 字段映射第 ' . $i . ' 行 输入字段名（input_field）参数无效！');
                    }

                    $inputField = $mapping['input_field'];

                    if (!isset($input->$inputField)) {
                        throw new ServiceException('节点 ' . $mapping['index'] . ' 字段映射第 ' . $i . ' 行 输入字段名（' . $inputField . '）在输入数据中不存在！');
                    }

                    $output->$field = $input->$inputField;

                } else {

                    if (!isset($mapping['custom']) || !is_string($mapping['custom']) || strlen($mapping['custom']) === 0) {
                        throw new ServiceException('节点 ' . $mapping['index'] . ' 字段映射第 ' . $i . ' 行 自定义值（custom）参数无效！');
                    }

                    $output->$field = $mapping['custom'];
                }

                $i++;
            }

        } else {

            if (!isset($formData['field_mapping_code']) || !is_string($formData['field_mapping_code']) || strlen($formData['field_mapping_code']) === 0) {
                throw new ServiceException('节点 ' . $formData['index'] . ' 代码处理（field_mapping_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): object {' . $formData['field_mapping_code'] . '};');
                $output = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $formData['index'] . ' 代码处理（field_mapping_code）执行出错：' . $t->getMessage());
            }
        }

        if (count( (array) $output ) === 0) {
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
