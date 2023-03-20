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
     * @param array $formDataNode 表单数据
     * @param object $input 输入数据
     * @return object
     * @throws \Throwable
     */
    public function test(array $formDataNode, object $input): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['field_mapping']) || !is_string($formDataNode['item']['field_mapping']) || !in_array($formDataNode['item']['field_mapping'], ['mapping', 'code'])) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射类型（field_mapping）参数无效！');
        }

        if ($formDataNode['item']['field_mapping'] === 'mapping') {

            if (!isset($formDataNode['item']['field_mapping_details']) || !is_array($formDataNode['item']['field_mapping_details'])) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射（field_mapping_details）参数无效！');
            }

            $output = new \stdClass();

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

                    $output->$field = $input->$inputField;

                } else {

                    if (!isset($mapping['custom']) || !is_string($mapping['custom'])) {
                        throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 自定义值（custom）参数无效！');
                    }

                    $output->$field = $mapping['custom'];
                }

                $i++;
            }

        } else {

            if (!isset($formDataNode['item']['field_mapping_code']) || !is_string($formDataNode['item']['field_mapping_code']) || strlen($formDataNode['item']['field_mapping_code']) === 0) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 代码处理（field_mapping_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): object {' . $formDataNode['item']['field_mapping_code'] . '};');
                $output = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 代码处理（field_mapping_code）执行出错：' . $t->getMessage());
            }
        }

        if (count( (array) $output ) === 0) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 输出数据为空！');
        }

        return $output;
    }

    /**
     * 插入数据库
     * @param string $flowNodeId
     * @param array $formDataNode
     * @return object
     */
    public function insert(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_output_csv');
        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->field_mapping = $formDataNode['item']['field_mapping'];
        $tupleFlowNodeItem->field_mapping_details = serialize($formDataNode['item']['field_mapping_details']);
        $tupleFlowNodeItem->field_mapping_code = $formDataNode['item']['field_mapping_code'];
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
        $nodeItem->field_mapping_details = unserialize($nodeItem->field_mapping_details);

        $nodeItem->output = unserialize($nodeItem->output);
        if ($nodeItem->output === '') {
            $nodeItem->output = false;
        }

        return $nodeItem;
    }


    /**
     * 计划任务处理数据
     *
     * @param object $flowNode 数据流节点
     * @param object $input 输入
     * @return object 输出
     * @throws \Throwable
     */
    public function process(object $flowNode, object $input): object
    {

    }


}
