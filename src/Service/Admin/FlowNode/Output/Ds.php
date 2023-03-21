<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Output;


use Be\App\Etl\Service\Admin\FlowNode\Output;
use Be\App\ServiceException;
use Be\Be;
use Be\Db\Driver;

class Ds extends Output
{

    public function getItemName(): string
    {
        return '数据源';
    }

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

        if (!isset($formDataNode['item']['ds_id']) || !is_string($formDataNode['item']['ds_id']) || strlen($formDataNode['item']['ds_id']) !== 36) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 数据源（ds_id）参数无效！');
        }

        if (!isset($formDataNode['item']['ds_table']) || !is_string($formDataNode['item']['ds_table']) || $formDataNode['item']['ds_table'] !== '') {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 数据表（ds_table）参数无效！');
        }

        if (!isset($formDataNode['item']['field_mapping']) || !is_string($formDataNode['item']['field_mapping']) || !in_array($formDataNode['item']['field_mapping'], ['mapping', 'code'])) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射类型（field_mapping）参数无效！');
        }

        $serviceDs = Be::getService('App.Etl.Admin.Ds');

        $tableFields = $serviceDs->getTableFields($formDataNode['item']['ds_id'], $formDataNode['item']['ds_table']);

        if ($formDataNode['item']['field_mapping'] === 'mapping') {

            if (!isset($formDataNode['item']['field_mapping_details']) || !is_array($formDataNode['item']['field_mapping_details'])) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射（field_mapping_details）参数无效！');
            }

            $output = new \stdClass();

            $i = 1;
            foreach ($formDataNode['item']['field_mapping_details'] as $mapping) {

                if (!isset($mapping['enable']) || !is_numeric($mapping['enable'])) {
                    throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 是否有效（enable）参数无效！');
                }

                $enable = (int)$mapping['enable'];

                if (!in_array($enable, [0, 1])) {
                    throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 是否有效（is_enable）参数无效！');
                }

                if ($enable === 0) continue;

                if (!isset($mapping['field']) || !is_string($mapping['field']) || $mapping['field'] !== '') {
                    throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 数据表字段名（field）参数无效！');
                }

                $field = $mapping['field'];

                $found = false;
                foreach ($tableFields as $tableField) {
                    if ($tableField['name'] === $field) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 数据表字段名（' . $field . '）不存在！');
                }

                if (!isset($mapping['type']) || !is_string($mapping['type']) || !in_array($mapping['type'], ['input_field', 'custom'])) {
                    throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 取值类型（type）参数无效！');
                }

                if ($mapping['type'] === 'input_field') {

                    if (!isset($mapping['input_field']) || !is_string($mapping['input_field']) || $mapping['input_field'] !== '') {
                        throw new ServiceException('节点 ' . $formDataNode['index'] . ' 字段映射第 ' . $i . ' 行 输入段名（input_field）参数无效！');
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

            if (!isset($formDataNode['item']['field_mapping_code']) || !is_string($formDataNode['item']['field_mapping_code']) || $formDataNode['item']['field_mapping_code'] !== '') {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 代码处理（field_mapping_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): object {' . $formDataNode['item']['field_mapping_code'] . '};');
                $output = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 代码处理（field_mapping_code）执行出错：' . $t->getMessage());
            }
        }

        if (count((array)$output) === 0) {
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
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_output_ds');
        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->ds_id = $formDataNode['item']['ds_id'];
        $tupleFlowNodeItem->ds_table = $formDataNode['item']['ds_table'];
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


    private ?array $fieldMappingDetails = null;
    private ?\Closure $fieldMappingFn = null;
    private ?Driver $db = null;


    public function start(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        if ($flowNode->item->field_mapping === 'mapping') {
            $this->fieldMappingDetails = unserialize($flowNode->item->field_mapping_details);
        } else {
            try {
                $this->fieldMappingFn = eval('return function(object $input): object {' . $flowNode->item->field_mapping_code . '};');
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $flowNode->index . ' 代码处理（field_mapping_code）执行出错：' . $t->getMessage());
            }
        }

        $this->db = Be::getService('App.Etl.Admin.Ds')->newDb($flowNode->item->ds_id);
    }


    public function process(object $flowNode, object $input, object $flowLog, object $flowNodeLog): object
    {
        if ($flowNode->item->field_mapping === 'mapping') {
            $output = new \stdClass();
            foreach ($this->fieldMappingDetails as $mapping) {
                if ($mapping['enable'] === 0) continue;

                $field = $mapping['field'];
                if ($mapping['type'] === 'input_field') {
                    $inputField = $mapping['input_field'];
                    $output->$field = $input->$inputField;
                } else {
                    $output->$field = $mapping['custom'];
                }
            }

        } else {
            try {
                $fn = $this->fieldMappingFn;
                $output = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $flowNode->index . ' 代码处理（field_mapping_code）执行出错：' . $t->getMessage());
            }
        }


        $this->db->insert($flowNode->item->ds_table, $output);

        return $output;
    }


    public function finish(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $this->fieldMappingDetails = null;
        $this->fieldMappingFn = null;
        $this->db = null;
    }


}
