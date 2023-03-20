<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Output;


use Be\App\Etl\Service\Admin\FlowNode\Output;
use Be\App\ServiceException;
use Be\Be;

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
    public function test(array $formDataNode, object $input): object
    {

        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        $output = new \stdClass();

        if (!isset($formDataNode['item']['url']) || !is_string($formDataNode['item']['url']) || strlen($formDataNode['item']['url']) === 0) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' API网址（url）参数无效！');
        }

        $output->url = $formDataNode['item']['url'];

        if (!isset($formDataNode['item']['headers']) || !is_array($formDataNode['item']['headers'])) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 请求头（headers）参数无效！');
        }

        if (count($formDataNode['item']['headers']) > 0) {
            $headers = [];

            $i = 1;
            foreach ($formDataNode['item']['headers'] as $header) {

                if (!isset($header['name']) || !is_string($header['name']) || strlen($header['name']) === 0) {
                    throw new ServiceException('节点 ' . $formDataNode['index'] . ' 请求头第 ' . $i . ' 行 名称（name）参数无效！');
                }

                if (!isset($header['value']) || !is_string($header['value']) || strlen($header['value']) === 0) {
                    throw new ServiceException('节点 ' . $formDataNode['index'] . ' 请求头第 ' . $i . ' 行 值（value）参数无效！');
                }

                $i++;

            }
            $output->headers = $headers;
        }

        if (!isset($formDataNode['item']['format']) || !is_string($formDataNode['item']['format']) || !in_array($formDataNode['item']['format'], ['form', 'json'])) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 请求格式（format）参数无效！');
        }

        $output->format = $formDataNode['item']['format'];


        if (!isset($formDataNode['item']['field_mapping']) || !is_string($formDataNode['item']['field_mapping']) || !in_array($formDataNode['item']['field_mapping'], ['mapping', 'code'])) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射类型（field_mapping）参数无效！');
        }

        $output->field_mapping = $formDataNode['item']['field_mapping'];


        if ($formDataNode['item']['field_mapping'] === 'mapping') {

            if (!isset($formDataNode['item']['field_mapping_details']) || !is_array($formDataNode['item']['field_mapping_details'])) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射（field_mapping_details）参数无效！');
            }

            $fields = new \stdClass();

            $i = 1;
            foreach ($formDataNode['item']['field_mapping_details'] as $mapping) {

                if (!isset($mapping['field']) || !is_string($mapping['field']) || strlen($mapping['field']) === 0) {
                    throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 列名（field）参数无效！');
                }

                $field = $mapping['field'];

                if (!isset($mapping['type']) || !is_string($mapping['type']) || !in_array($mapping['type'], ['input_field', 'custom'])) {
                    throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 取值类型（type）参数无效！');
                }

                if ($mapping['type'] === 'input_field') {

                    if (!isset($mapping['input_field']) || !is_string($mapping['input_field']) || strlen($mapping['input_field']) === 0) {
                        throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 输入字段名（input_field）参数无效！');
                    }

                    $inputField = $mapping['input_field'];

                    if (!isset($input->$inputField)) {
                        throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 输入字段名（' . $inputField . '）在输入数据中不存在！');
                    }

                    $fields->$field = $input->$inputField;

                } else {

                    if (!isset($mapping['custom']) || !is_string($mapping['custom']) || strlen($mapping['custom']) === 0) {
                        throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 自定义值（custom）参数无效！');
                    }

                    $fields->$field = $mapping['custom'];
                }

                $i++;
            }

            $output->fields = $fields;

        } else {

            if (!isset($formDataNode['item']['field_mapping_code']) || !is_string($formDataNode['item']['field_mapping_code']) || strlen($formDataNode['item']['field_mapping_code']) === 0) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 代码处理（field_mapping_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): object {' . $formDataNode['item']['field_mapping_code'] . '};');
                $output->fields = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 代码处理（field_mapping_code）执行出错：' . $t->getMessage());
            }
        }

        if (count( (array) $output->fields ) === 0) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段列表为空！');
        }

        if (!isset($formDataNode['item']['interval']) || !is_numeric($formDataNode['item']['interval'])) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 间隔时间（毫秒）（interval）参数无效！');
        }

        $formDataNode['item']['interval'] = (int) $formDataNode['item']['interval'];

        if ($formDataNode['item']['interval'] < 0) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 间隔时间（毫秒）（interval）参数无效！');
        }

        $output->interval = $formDataNode['item']['interval'];

        if (!isset($formDataNode['item']['success_mark']) || !is_string($formDataNode['item']['success_mark']) || strlen($formDataNode['item']['success_mark']) === 0) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 成功标记（success_mark）参数无效！');
        }

        $output->success_mark = $formDataNode['item']['success_mark'];

        return $output;
    }

    /**
     * 插入数据库
     * @param string $flowNodeId
     * @param array $formData
     * @return object
     */
    public function insert(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_output_api');
        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->url = $formDataNode['item']['url'];
        $tupleFlowNodeItem->headers = serialize($formDataNode['item']['headers']);
        $tupleFlowNodeItem->format = $formDataNode['item']['format'];
        $tupleFlowNodeItem->field_mapping = $formDataNode['item']['field_mapping'];
        $tupleFlowNodeItem->field_mapping_details = serialize($formDataNode['item']['field_mapping_details']);
        $tupleFlowNodeItem->field_mapping_code = $formDataNode['item']['field_mapping_code'];
        $tupleFlowNodeItem->success_mark = $formDataNode['item']['success_mark'];
        $tupleFlowNodeItem->interval = $formDataNode['item']['interval'];
        $tupleFlowNodeItem->output = serialize($formDataNode['item']['output']);
        $tupleFlowNodeItem->create_time = date('Y-m-d H:i:s');
        $tupleFlowNodeItem->update_time = date('Y-m-d H:i:s');
        $tupleFlowNodeItem->insert();
        return $tupleFlowNodeItem->toObject();
    }


    /**
     * 格式化数据库中读取出来的数据
     *
     * @param object $nodeItem
     * @return object
     */
    public function format(object $nodeItem): object
    {
        $nodeItem->headers = unserialize($nodeItem->headers);

        $nodeItem->field_mapping_details = unserialize($nodeItem->field_mapping_details);

        $nodeItem->output = unserialize($nodeItem->output);
        if ($nodeItem->output === '') {
            $nodeItem->output = false;
        }

        $nodeItem->interval = (int) $nodeItem->interval;

        return $nodeItem;
    }


    public function handle(object $input): array
    {
        // TODO: Implement handle() method.
    }
}
