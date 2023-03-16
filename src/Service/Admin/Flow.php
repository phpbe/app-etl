<?php

namespace Be\App\Etl\Service\Admin;


use Be\App\ServiceException;
use Be\Be;

class Flow
{

    /**
     * 编辑数据流
     *
     * @param array $formData 数据流数据
     * @return object
     * @throws \Throwable
     */
    public function edit(array $formData): object
    {
        $db = Be::getDb();

        $isNew = true;
        $flowId = null;
        if (isset($formData['id']) && $formData['id'] !== '') {
            $isNew = false;
            $flowId = $formData['id'];
        }

        $tupleFlow = Be::getTuple('etl_flow');
        if (!$isNew) {
            try {
                $tupleFlow->load($flowId);
            } catch (\Throwable $t) {
                throw new ServiceException('数据流（# ' . $flowId . '）不存在！');
            }
        }

        if (!isset($formData['name']) || !is_string($formData['name'])) {
            throw new ServiceException('数据流名称未填写！');
        }

        if (!isset($formData['category_id']) || !is_string($formData['category_id'])) {
            throw new ServiceException('分类未填写！');
        }


        if (!isset($formData['is_enable']) || !is_numeric($formData['is_enable'])) {
            $formData['is_enable'] = 0;
        } else {
            $formData['is_enable'] = (int)$formData['is_enable'];
        }
        if (!in_array($formData['is_enable'], [-1, 0, 1])) {
            $formData['is_enable'] = 0;
        }

        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $tupleFlow->name = $formData['name'];
            $tupleFlow->category_id = $formData['category_id'];
            $tupleFlow->is_enable = $formData['is_enable'];
            $tupleFlow->update_time = $now;
            if ($isNew) {
                $tupleFlow->create_time = $now;
                $tupleFlow->insert();
            } else {
                $tupleFlow->update();
            }

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);
            throw new ServiceException(($isNew ? '新建' : '编辑') . '数据流发生异常！');
        }

        return $tupleFlow->toObject();



        $db = Be::getDb();
        $db->startTransaction();
        try {
            $postData = $request->json();
            $formData = $postData['formData'];

            $tuple = Be::getTuple('etl_extract');
            if (isset($formData['id']) && $formData['id']) {
                $tuple->load($formData['id']);
            } else {
                $tuple->field_mapping_type = 'same';
                $tuple->field_mapping = '';
                $tuple->field_mapping_code = '';
                $tuple->breakpoint_type = 'full';
                $tuple->breakpoint_field = '';
                $tuple->breakpoint = '1970-01-02 00:00:00';
                $tuple->breakpoint_step = '1_DAY';
                $tuple->breakpoint_offset = 0;
                $tuple->schedule = '';
                $tuple->is_enable = 1;
                $tuple->is_delete = 0;
                $tuple->create_time = date('Y-m-d H:i:s');
            }

            if ($formData['step'] == '0') {
                $tuple->name = $formData['name'];
                $tuple->category_id = $formData['category_id'];
                $tuple->src_ds_id = $formData['src_ds_id'];
                $tuple->dst_ds_id = $formData['dst_ds_id'];
                $tuple->src_type = $formData['src_type'];
                $tuple->src_table = $formData['src_table'];
                $tuple->src_sql = $formData['src_sql'];
                $tuple->dst_table = $formData['dst_table'];
            } elseif ($formData['step'] == '1') {
                $tuple->field_mapping_type = $formData['field_mapping_type'];
                $tuple->field_mapping = '';
                $tuple->field_mapping_code = '';
                if ($tuple->field_mapping_type == 'mapping') {
                    $tuple->field_mapping = $formData['field_mapping'];
                } elseif ($tuple->field_mapping_type == 'code') {
                    $tuple->field_mapping_code = $formData['field_mapping_code'];
                }
            } elseif ($formData['step'] == '2') {
                $tuple->breakpoint_type = $formData['breakpoint_type'];
                if ($tuple->breakpoint_type == 'full') {
                    $tuple->breakpoint_field = '';
                    $tuple->breakpoint = date('Y-m-d H:i:s', 0);
                    $tuple->breakpoint_step = '';
                    $tuple->breakpoint_offset = 0;
                } else {
                    $tuple->breakpoint_field = $formData['breakpoint_field'];
                    $tuple->breakpoint = $formData['breakpoint'];
                    $tuple->breakpoint_step = $formData['breakpoint_step'];
                    $tuple->breakpoint_offset = $formData['breakpoint_offset'];
                }
                $tuple->schedule = $formData['schedule'];
            }
            $tuple->update_time = date('Y-m-d H:i:s');
            $tuple->save();

            $db->commit();
            $response->set('success', true);
            $response->set('flow', $tuple->toObject());
            $response->json();
        } catch (\Exception $e) {
            $db->rollback();
            $response->error($e->getMessage());
        }
    }

    /**
     * 获取数据流
     *
     * @param string $flowId
     * @return object
     */
    public function getFlow(string $flowId): object
    {
        $tupleFlow = Be::getTuple('etl_flow');
        try {
            $tupleFlow->load($flowId);
        } catch (\Throwable $t) {
            throw new ServiceException('数据流（# ' . $flowId . '）不存在！');
        }

        $flow = $tupleFlow->toObject();

        $nodes = Be::getTable('etl_flow_node')
            ->where('flow_id', $flowId)
            ->orderBy('index', 'asc')
            ->getObjects();

        foreach ($nodes as $node) {
            $tupleNodeItem = Be::getTuple('etl_flow_' . $node->item_type);
            try {
                $tupleNodeItem->load($node->item_id);
            } catch (\Throwable $t) {
                throw new ServiceException('数据流 子项（# ' . $node->item_id . '）不存在！');
            }

            $node->item = $tupleNodeItem->toObject();
        }

        $flow->nodes = $nodes;

        return $flow;
    }


    public function getIdNameKeyValues()
    {
        return Be::getTable('etl_flow')
            ->where('is_delete', 0)
            ->where('is_enable', 1)
            ->getKeyValues('id', 'name');
    }


    public function getDsTypeKeyValues()
    {
        return [
            'table' => '表',
            'sql' => 'SQL语句',
        ];
    }

    public function getFieldMappingKeyValues()
    {
        return [
            'mapping' => '字段映射',
            'code' => '代码处理',
        ];
    }

    public function getBreakpointKeyValues()
    {
        return [
            'full' => '全量',
            'breakpoint' => '有断点',
        ];
    }

    public function getBreakpointStepKeyValues()
    {
        return [
            '1_HOUR' => '一小时',
            '1_DAY' => '一天',
            '1_MONTH' => '一个月',
        ];
    }

}
