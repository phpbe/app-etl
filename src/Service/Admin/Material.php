<?php

namespace Be\App\Etl\Service\Admin;


use Be\App\ServiceException;
use Be\Be;

class Material
{

    /**
     * 获取素材
     *
     * @param string $materialId
     * @return object
     */
    public function getMaterial(string $materialId): object
    {
        $tupleMaterial = Be::getTuple('etl_material');
        try {
            $tupleMaterial->load($materialId);
        } catch (\Throwable $t) {
            throw new ServiceException('素材（# ' . $materialId . '）不存在！');
        }

        $material = $tupleMaterial->toObject();

        $fields = unserialize($material->fields);

        /*
        foreach ($fields as $field) {
        }
        */

        $material->fields = $fields;

        return $material;
    }

    /**
     * 获取素材 id -> name 键值对
     * @return array
     */
    public function getIdNameKeyValues()
    {
        return Be::getTable('etl_material')->getKeyValues('id', 'name');
    }

    /**
     * 获取素材  id -> label 键值对
     * @return array
     */
    public function getIdLabelKeyValues()
    {
        return Be::getTable('etl_material')->getKeyValues('id', 'label');
    }

    /**
     * 获取素材  name -> label 键值对
     * @return array
     */
    public function getNameLabelKeyValues()
    {
        return Be::getTable('etl_material')->getKeyValues('name', 'label');
    }

    /**
     * 编辑素材
     *
     * @param array $formData 素材数据
     * @return object
     * @throws \Throwable
     */
    public function edit(array $formData): object
    {
        $isNew = true;
        $materialId = null;
        if (isset($formData['id']) && $formData['id'] !== '') {
            $isNew = false;
            $materialId = $formData['id'];
        }

        $tupleMaterial = Be::getTuple('etl_material');
        if (!$isNew) {
            try {
                $tupleMaterial->load($materialId);
            } catch (\Throwable $t) {
                throw new ServiceException('素材（# ' . $materialId . '）不存在！');
            }
        }

        if (!isset($formData['name']) || !is_string($formData['name']) || $formData['name'] === '') {
            throw new ServiceException('素材英文名称（name）未填写！');
        }

        if ($isNew) {
            $nameExist = Be::getTable('etl_material')
                    ->where('name', $formData['name'])
                    ->getValue('COUNT(*)') > 0;
        } else {
            $nameExist = Be::getTable('etl_material')
                    ->where('name', $formData['name'])
                    ->where('id', '!=', $materialId)
                    ->getValue('COUNT(*)') > 0;
        }

        if ($nameExist) {
            throw new ServiceException('素材英文名称（' . $formData['name'] . '）已存在！');
        }

        if (!isset($formData['label']) || !is_string($formData['label']) || $formData['label'] === '') {
            throw new ServiceException('素材中文名称（label）未填写！');
        }

        if (!isset($formData['fields']) || !is_array($formData['fields'])) {
            throw new ServiceException('节点 ' . ($formData['index'] + 1) . ' 字段映射（fields）参数无效！');
        }

        $fields = [];

        $names = [];

        $i = 1;
        foreach ($formData['fields'] as $field) {

            $f = new \stdClass();


            if (!isset($field['name']) || !is_string($field['name']) || $field['name'] === '') {
                throw new ServiceException('字段 ' . $i . ' 字段英文名（name）参数无效！');
            }
            $f->name = $field['name'];

            if (isset($names[$f->name])) {
                throw new ServiceException('字段 ' . $names[$f->name] . ' 和字段 ' . $i . ' 字段英文名（name）重复！');
            }

            $names[$f->name] = $i;


            if (!isset($field['label']) || !is_string($field['label']) || $field['label'] === '') {
                throw new ServiceException('字段 ' . $i . ' 字段中文名（label）参数无效！');
            }
            $f->label = $field['label'];


            if (!isset($field['type']) || !is_string($field['type']) || !in_array($field['type'], ['text', 'html', 'int', 'float', 'bool', 'date', 'datetime'])) {
                throw new ServiceException('字段 ' . $i . ' 数据类型（type）参数无效！');
            }
            $f->type = $field['type'];


            if (!isset($field['default']) || !is_string($field['default'])) {
                throw new ServiceException('字段 ' . $i . ' 默认值（default）参数无效！');
            }
            $f->default = $field['default'];


            if (!isset($field['length']) || !is_numeric($field['length'])) {
                throw new ServiceException('字段 ' . $i . ' 最大长度（length）参数无效！');
            }
            $f->length = (int)$field['length'];


            if (!isset($field['required']) || !is_numeric($field['required']) || !in_array($field['required'], ['0', '1'])) {
                throw new ServiceException('字段 ' . $i . ' 是否必填（required）参数无效！');
            }
            $f->required = (int)$field['required'];


            if (!isset($field['unique']) || !is_numeric($field['unique']) || !in_array($field['unique'], ['0', '1'])) {
                throw new ServiceException('字段 ' . $i . ' 是否唯一（unique）参数无效！');
            }
            $f->unique = (int)$field['unique'];

            /*
            if ($f->unique === 1) {
                if ($f->length > 180) {
                    throw new ServiceException('字段 ' . $i . ' 设置唯一键时，长度不可超过 180！');
                }
            }
            */

            $fields[] = $f;

            $i++;
        }

        $db = Be::getDb();
        $db->startTransaction();
        try {

            $tupleMaterial->name = $formData['name'];
            $tupleMaterial->label = $formData['label'];
            $tupleMaterial->fields = serialize($fields);
            $tupleMaterial->update_time = date('Y-m-d H:i:s');;
            if ($isNew) {
                $tupleMaterial->create_time = date('Y-m-d H:i:s');;
                $tupleMaterial->insert();
            } else {
                $tupleMaterial->update();
            }

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException('编辑素材发生异常：' . $t->getMessage());
        }

        return $tupleMaterial->toObject();
    }


}
