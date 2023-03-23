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


    public function test(array $formDataNode, object $input): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['ds_id']) || !is_string($formDataNode['item']['ds_id']) || strlen($formDataNode['item']['ds_id']) !== 36) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 数据源（ds_id）参数无效！');
        }

        if (!isset($formDataNode['item']['ds_table']) || !is_string($formDataNode['item']['ds_table']) || $formDataNode['item']['ds_table'] === '') {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 数据表（ds_table）参数无效！');
        }

        if (!isset($formDataNode['item']['clean']) || !is_numeric($formDataNode['item']['clean'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 运行前清空数据表（clean）参数无效！');
        }

        $formDataNode['item']['clean'] = (int)$formDataNode['item']['clean'];

        if (!in_array($formDataNode['item']['clean'], [0, 1])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 运行前清空数据表（clean）参数无效！');
        }

        if ($formDataNode['item']['clean'] === 1) {
            if (!isset($formDataNode['item']['clean_type']) || !is_string($formDataNode['item']['clean_type']) || !in_array($formDataNode['item']['clean_type'], ['truncate', 'delete'])) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 清空数据表方式（clean_type）参数无效！');
            }
        }

        if (!isset($formDataNode['item']['on_duplicate_update']) || !is_numeric($formDataNode['item']['on_duplicate_update'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 重复数据执行更新（on_duplicate_update）参数无效！');
        }

        $formDataNode['item']['on_duplicate_update'] = (int)$formDataNode['item']['on_duplicate_update'];

        if (!in_array($formDataNode['item']['on_duplicate_update'], [0, 1])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 重复数据执行更新（on_duplicate_update）参数无效！');
        }

        if ($formDataNode['item']['on_duplicate_update'] === 1) {
            if (!isset($formDataNode['item']['on_duplicate_update_field']) || !is_string($formDataNode['item']['on_duplicate_update_field']) || $formDataNode['item']['on_duplicate_update_field'] === '') {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 重复数据执行更新检测字段（on_duplicate_update_field）参数无效！');
            }
        } else {

            if (!isset($formDataNode['item']['mysql_replace']) || !is_numeric($formDataNode['item']['mysql_replace'])) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 是否启用 MYSQL 数据库 Replace Into（mysql_replace）参数无效！');
            }

            $formDataNode['item']['mysql_replace'] = (int)$formDataNode['item']['mysql_replace'];

            if (!in_array($formDataNode['item']['mysql_replace'], [0, 1])) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 是否启用 MYSQL 数据库 Replace Into（mysql_replace）参数无效！');
            }

        }

        if (!isset($formDataNode['item']['field_mapping']) || !is_string($formDataNode['item']['field_mapping']) || !in_array($formDataNode['item']['field_mapping'], ['mapping', 'code'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射类型（field_mapping）参数无效！');
        }

        $serviceDs = Be::getService('App.Etl.Admin.Ds');

        $tableFields = $serviceDs->getTableFields($formDataNode['item']['ds_id'], $formDataNode['item']['ds_table']);

        if ($formDataNode['item']['field_mapping'] === 'mapping') {

            if (!isset($formDataNode['item']['field_mapping_details']) || !is_array($formDataNode['item']['field_mapping_details'])) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射（field_mapping_details）参数无效！');
            }

            $output = new \stdClass();

            $i = 1;
            foreach ($formDataNode['item']['field_mapping_details'] as $mapping) {

                if (!isset($mapping['enable']) || !is_numeric($mapping['enable'])) {
                    throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射第 ' . $i . ' 行 是否有效（enable）参数无效！');
                }

                $enable = (int)$mapping['enable'];

                if (!in_array($enable, [0, 1])) {
                    throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射第 ' . $i . ' 行 是否有效（is_enable）参数无效！');
                }

                if ($enable === 0) continue;

                if (!isset($mapping['field']) || !is_string($mapping['field']) || $mapping['field'] === '') {
                    throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射第 ' . $i . ' 行 数据表字段名（field）参数无效！');
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
                    throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射第 ' . $i . ' 行 数据表字段名（' . $field . '）不存在！');
                }

                if (!isset($mapping['type']) || !is_string($mapping['type']) || !in_array($mapping['type'], ['input_field', 'custom'])) {
                    throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射第 ' . $i . ' 行 取值类型（type）参数无效！');
                }

                if ($mapping['type'] === 'input_field') {

                    if (!isset($mapping['input_field']) || !is_string($mapping['input_field']) || $mapping['input_field'] === '') {
                        throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射第 ' . $i . ' 行 输入段名（input_field）参数无效！');
                    }

                    $inputField = $mapping['input_field'];

                    if (!isset($input->$inputField)) {
                        throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射第 ' . $i . ' 行 输入字段名（' . $inputField . '）在输入数据中不存在！');
                    }

                    $output->$field = $input->$inputField;

                } else {

                    if (!isset($mapping['custom']) || !is_string($mapping['custom'])) {
                        throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射第 ' . $i . ' 行 自定义值（custom）参数无效！');
                    }

                    $output->$field = $mapping['custom'];
                }

                $i++;
            }

        } else {

            if (!isset($formDataNode['item']['field_mapping_code']) || !is_string($formDataNode['item']['field_mapping_code']) || $formDataNode['item']['field_mapping_code'] === '') {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 代码处理（field_mapping_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): object {' . $formDataNode['item']['field_mapping_code'] . '};');
                $output = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 代码处理（field_mapping_code）执行出错：' . $t->getMessage());
            }
        }

        if (count((array)$output) === 0) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 输出数据为空！');
        }

        return $output;
    }


    public function edit(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_output_ds');

        if (isset($formDataNode['item']['id']) && is_string($formDataNode['item']['id']) && strlen($formDataNode['item']['id']) === 36) {
            try {
                $tupleFlowNodeItem->load($formDataNode['item']['id']);
            } catch (\Throwable $t) {
            }
        }

        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->ds_id = $formDataNode['item']['ds_id'];
        $tupleFlowNodeItem->ds_table = $formDataNode['item']['ds_table'];
        $tupleFlowNodeItem->clean = $formDataNode['item']['clean'];
        $tupleFlowNodeItem->clean_type = $formDataNode['item']['clean_type'];
        $tupleFlowNodeItem->on_duplicate_update = $formDataNode['item']['on_duplicate_update'];
        $tupleFlowNodeItem->on_duplicate_update_field = $formDataNode['item']['on_duplicate_update_field'];
        $tupleFlowNodeItem->mysql_replace = $formDataNode['item']['mysql_replace'];
        $tupleFlowNodeItem->field_mapping = $formDataNode['item']['field_mapping'];
        $tupleFlowNodeItem->field_mapping_details = serialize($formDataNode['item']['field_mapping_details']);
        $tupleFlowNodeItem->field_mapping_code = $formDataNode['item']['field_mapping_code'];
        $tupleFlowNodeItem->output = serialize($formDataNode['item']['output']);

        $tupleFlowNodeItem->update_time = date('Y-m-d H:i:s');

        if ($tupleFlowNodeItem->isLoaded()) {
            $tupleFlowNodeItem->update();
        } else {
            $tupleFlowNodeItem->create_time = date('Y-m-d H:i:s');
            $tupleFlowNodeItem->insert();
        }

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
        $flowNode->item->clean = (int) $flowNode->item->clean;
        $flowNode->item->on_duplicate_update = (int) $flowNode->item->on_duplicate_update;
        $flowNode->item->mysql_replace = (int) $flowNode->item->mysql_replace;

        if ($flowNode->item->field_mapping === 'mapping') {
            $this->fieldMappingDetails = unserialize($flowNode->item->field_mapping_details);
        } else {
            try {
                $this->fieldMappingFn = eval('return function(object $input): object {' . $flowNode->item->field_mapping_code . '};');
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 代码处理（field_mapping_code）执行出错：' . $t->getMessage());
            }
        }

        $this->db = Be::getService('App.Etl.Admin.Ds')->newDb($flowNode->item->ds_id);

        // 清空数据
        if ($flowNode->item->clean === 1) {
            if ($flowNode->item->clean_type === 'truncate') {
                $sql = 'TRUNCATE TABLE ' . $this->db->quoteKey($flowNode->item->ds_table);
            } else {
                $sql = 'DELETE FROM ' . $this->db->quoteKey($flowNode->item->ds_table);
            }

            $this->db->query($sql);
        }
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
                    if ($mapping['custom'] === 'uuid()') {
                        $output->$field = $this->db->uuid();
                    } else {
                        $output->$field = $mapping['custom'];
                    }
                }
            }

        } else {
            try {
                $fn = $this->fieldMappingFn;
                $output = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 代码处理（field_mapping_code）执行出错：' . $t->getMessage());
            }
        }


        if ($flowNode->item->on_duplicate_update === 1) {
            $sql = 'SELECT COUNT(*) FROM ' . $this->db->quoteKey($flowNode->item->ds_table) . ' WHERE ';
            $field = $flowNode->item->on_duplicate_update_field;
            $sql .= $this->db->quoteKey($field) . ' = ' . $this->db->quoteValue($output->$field);

            $count = (int)$this->db->getValue($sql);
            if ($count > 0) {
                $this->db->update($flowNode->item->ds_table, $output, $field);
            } else {
                $this->db->insert($flowNode->item->ds_table, $output);
            }
        } else {
            if ($flowNode->item->mysql_replace === 1) {
                $this->db->replace($flowNode->item->ds_table, $output);
            } else {
                $this->db->insert($flowNode->item->ds_table, $output);
            }
        }

        return $output;
    }


    public function finish(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $this->fieldMappingDetails = null;
        $this->fieldMappingFn = null;
        $this->db = null;
    }


}
