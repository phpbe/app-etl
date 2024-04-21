<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Output;


use Be\App\Etl\Service\Admin\FlowNode\Output;
use Be\App\ServiceException;
use Be\Be;
use Be\Db\Driver;

class Material extends Output
{

    public function getItemName(): string
    {
        return '素材';
    }


    public function test(array $formDataNode, object $input): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['material_id']) || !is_string($formDataNode['item']['material_id']) || strlen($formDataNode['item']['material_id']) !== 36) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 素材（material_id）参数无效！');
        }

        if (!isset($formDataNode['item']['field_mapping']) || !is_string($formDataNode['item']['field_mapping']) || !in_array($formDataNode['item']['field_mapping'], ['mapping', 'code'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射类型（field_mapping）参数无效！');
        }

        $material = Be::getService('App.Etl.Admin.Material')->getMaterial($formDataNode['item']['material_id']);

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
                    throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射第 ' . $i . ' 行 素材字段名（field）参数无效！');
                }

                $field = $mapping['field'];

                $found = false;
                foreach ($material->fields as $materialField) {
                    if ($materialField->name === $field) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 字段映射第 ' . $i . ' 行 素材字段名（' . $field . '）不存在！');
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

        if (!isset($formDataNode['item']['op']) || !is_string($formDataNode['item']['op']) || !in_array($formDataNode['item']['op'], ['auto', 'insert', 'update', 'delete'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 数据操作类型（op）参数无效！');
        }

        $formDataNode['item']['clean'] = 0;
        if ($formDataNode['item']['op'] === 'insert') {
            if (!isset($formDataNode['item']['clean']) || !is_numeric($formDataNode['item']['clean'])) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 运行前清空素材（clean）参数无效！');
            }

            $formDataNode['item']['clean'] = (int)$formDataNode['item']['clean'];

            if (!in_array($formDataNode['item']['clean'], [0, 1])) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 运行前清空素材（clean）参数无效！');
            }
        }

        return $output;
    }


    public function edit(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_output_material');

        if (isset($formDataNode['item']['id']) && is_string($formDataNode['item']['id']) && strlen($formDataNode['item']['id']) === 36) {
            try {
                $tupleFlowNodeItem->load($formDataNode['item']['id']);
            } catch (\Throwable $t) {
            }
        }

        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->material_id = $formDataNode['item']['material_id'];
        $tupleFlowNodeItem->field_mapping = $formDataNode['item']['field_mapping'];
        $tupleFlowNodeItem->field_mapping_details = serialize($formDataNode['item']['field_mapping_details']);
        $tupleFlowNodeItem->field_mapping_code = $formDataNode['item']['field_mapping_code'];
        $tupleFlowNodeItem->op = $formDataNode['item']['op'];
        $tupleFlowNodeItem->clean = $formDataNode['item']['clean'];
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
    private ?object $material = null;

    public function start(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $flowNode->item->clean = (int)$flowNode->item->clean;

        if ($flowNode->item->field_mapping === 'mapping') {
            $this->fieldMappingDetails = unserialize($flowNode->item->field_mapping_details);
        } else {
            try {
                $this->fieldMappingFn = eval('return function(object $input): object {' . $flowNode->item->field_mapping_code . '};');
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 代码处理（field_mapping_code）执行出错：' . $t->getMessage());
            }
        }

        // 清空数据
        if ($flowNode->item->clean === 1) {
            $db = Be::getDb();
            $sql = 'DELETE FROM ' . $db->quoteKey('etl_material_item') . ' WHERE ' . $db->quoteKey('material_id') . '=' . $db->quoteValue($flowNode->item->material_id);
            $db->query($sql);
        }

        $this->material = Be::getService('App.Etl.Admin.Material')->getMaterial($flowNode->item->material_id);
    }



    public function process(object $flowNode, object $input, object $flowLog, object $flowNodeLog): object
    {
        $db = Be::getDb();

        if ($flowNode->item->field_mapping === 'mapping') {
            $output = new \stdClass();
            foreach ($this->fieldMappingDetails as $mapping) {
                $field = $mapping['field'];
                if ($mapping['type'] === 'input_field') {
                    $inputField = $mapping['input_field'];
                    $output->$field = $input->$inputField;
                } else {
                    if ($mapping['custom'] === 'uuid()') {
                        $output->$field = $db->uuid();
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

        $m = new \stdClass();

        $uniqueKey = '';
        $data = [];
        foreach ($this->material->fields as $field) {
            $fieldName = $field->name;
            if (isset($output->$fieldName)) {
                $data[$fieldName] = $output->$fieldName;
            } else {
                $data[$fieldName] = $field->default;
            }

            if ($field->unique === 1) {
                $uniqueKey = $data[$fieldName];
            }
        }
        $m->data = serialize($data);

        if ($uniqueKey === '') {
            if ($flowNode->item->op === 'auto' || $flowNode->item->op === 'update' || $flowNode->item->op === 'delete') {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 目标素材未配置唯一键，"数据操作类型" 仅可使用 插入');
            }
        }

        if ($flowNode->item->op === 'auto' || $flowNode->item->op === 'update') {

            $sql = 'SELECT id, create_time, update_time FROM ' . $db->quoteKey('etl_material_item') . ' WHERE ';
            $sql .= $db->quoteKey('material_id') . ' = ' . $db->quoteValue($flowNode->item->material_id);
            $sql .= ' AND ' . $db->quoteKey('unique_key') . ' = ' . $db->quoteValue($uniqueKey);

            $existM = $db->getObject($sql);
            if ($existM > 0) {

                $m->id = $existM->id;
                $m->material_id = $flowNode->item->material_id;
                $m->unique_key = $uniqueKey;
                $m->create_time = $existM->create_time;
                $m->update_time = $existM->update_time;

                $db->update('etl_material_item', $m, 'id');
            } else {

                if ($flowNode->item->op === 'auto') {
                    $m->id = $db->uuid();
                    $m->material_id = $flowNode->item->material_id;
                    $m->unique_key = $uniqueKey;
                    $m->create_time = date('Y-m-d H:i:s');
                    $m->update_time = date('Y-m-d H:i:s');

                    $db->insert('etl_material_item', $m);
                }
            }

        } elseif ($flowNode->item->op === 'insert') {

            $m->id = $db->uuid();
            $m->material_id = $flowNode->item->material_id;
            $m->unique_key = $uniqueKey;
            $m->create_time = date('Y-m-d H:i:s');
            $m->update_time = date('Y-m-d H:i:s');

            $db->insert('etl_material_item', $m);

        } elseif ($flowNode->item->op === 'delete') {

            $sql = 'DELETE FROM ' . $db->quoteKey('etl_material_item') . ' WHERE ';
            $sql .= $db->quoteKey('material_id') . ' = ' . $db->quoteValue($flowNode->item->material_id);
            $sql .= ' AND ' . $db->quoteKey('unique_key') . ' = ' . $db->quoteValue($uniqueKey);

            $db->query($sql);

        }

        return $output;
    }


    public function finish(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $this->fieldMappingDetails = null;
        $this->fieldMappingFn = null;
        $this->material = null;
    }


}
