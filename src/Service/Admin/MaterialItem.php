<?php

namespace Be\App\Etl\Service\Admin;


use Be\App\ServiceException;
use Be\Be;

class MaterialItem
{

    /**
     * 获取素材
     *
     * @param string $materialItemId
     * @return object
     */
    public function getMaterialItem(string $materialItemId): object
    {
        $tupleMaterialItemItem = Be::getTuple('etl_material_item');
        try {
            $tupleMaterialItemItem->load($materialItemId);
        } catch (\Throwable $t) {
            throw new ServiceException('素材内容（# ' . $materialItemId . '）不存在！');
        }

        $materialItem = $tupleMaterialItemItem->toObject();

        $data = unserialize($materialItem->data);

        /*
        foreach ($fields as $field) {
        }
        */

        $materialItem->data = $data;

        return $materialItem;
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
        $materialItemId = null;
        if (isset($formData['id']) && $formData['id'] !== '') {
            $isNew = false;
            $materialItemId = $formData['id'];
        }

        $tupleMaterialItem = Be::getTuple('etl_material_item');
        if (!$isNew) {
            try {
                $tupleMaterialItem->load($materialItemId);
            } catch (\Throwable $t) {
                throw new ServiceException('素材内容（# ' . $materialItemId . '）不存在！');
            }
        }

        $this->processData($tupleMaterialItem, $formData);

        //$db = Be::getDb();
        //$db->startTransaction();
        try {

            $tupleMaterialItem->material_id = $formData['material_id'];
            $tupleMaterialItem->update_time = date('Y-m-d H:i:s');;
            if ($isNew) {
                $tupleMaterialItem->create_time = date('Y-m-d H:i:s');;
                $tupleMaterialItem->insert();
            } else {
                $tupleMaterialItem->update();
            }

            //$db->commit();

        } catch (\Throwable $t) {
            //$db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException('编辑素材内容发生异常：' . $t->getMessage());
        }

        return $tupleMaterialItem->toObject();
    }


    private $materials = [];

    public function processData($tuple, $formData)
    {
        if (!isset($formData['material_id']) || !is_string($formData['material_id']) || $formData['material_id'] === '') {
            throw new ServiceException('素材ID（material_id）缺失！');
        }

        $materialId = $formData['material_id'];

        if (isset($this->materials[$materialId])) {
            $material = $this->materials[$materialId];
        } else {
            $material = Be::getService('App.Etl.Admin.Material')->getMaterial($materialId);
            $this->materials[$materialId] = $material;
        }

        $isNew = true;
        $materialItemId = null;
        if (isset($formData['id']) && $formData['id'] !== '') {
            $isNew = false;
            $materialItemId = $formData['id'];
        }

        $uniqueKey = '';
        $data = [];

        $uniqueField = null;
        foreach ($material->fields as &$field) {

            if ($field->required === 1) {
                switch ($field->type) {
                    case 'text':
                    case 'textarea':
                    case 'html':
                        if (!isset($formData[$field->name]) || !is_string($formData[$field->name]) || $formData[$field->name] === '') {
                            throw new ServiceException('素材内容' . $field->label . '（' . $formData[$field->name] . '）无效！');
                        }
                        break;
                    case 'int':
                    case 'float':
                    case 'bool':
                        if (!isset($formData[$field->name]) || !is_numeric($formData[$field->name]) || $formData[$field->name] === '') {
                            throw new ServiceException('素材内容' . $field->label . '（' . $formData[$field->name] . '）无效！');
                        }
                        break;
                    case 'date':
                    case 'datetime':
                        if (!strtotime($formData[$field->name])) {
                            throw new ServiceException('素材内容' . $field->label . '（' . $formData[$field->name] . '）无效！');
                        }
                        break;
                }
            }


            switch ($field->type) {
                case 'text':
                case 'textarea':
                case 'html':
                    if (!isset($formData[$field->name]) || !is_string($formData[$field->name]) || $formData[$field->name] === '') {
                        $formData[$field->name] = $field->default;
                    }

                    if ($field->length > 0) {
                        if (mb_strlen($formData[$field->name]) > $field->length) {
                            $formData[$field->name] = mb_substr($formData[$field->name], 0, $field->length);
                        }
                    }

                    break;
                case 'int':
                    $formData[$field->name] = (int)$formData[$field->name];
                    break;
                case 'float':
                    $formData[$field->name] = (float)$formData[$field->name];
                    break;
                case 'bool':
                    $formData[$field->name] = $formData[$field->name] ? 1 : 0;
                    break;
                case 'date':
                    if (!strtotime($formData[$field->name])) {
                        $formData[$field->name] = date('Y-m-d');
                    }
                    break;
                case 'datetime':
                    if (!strtotime($formData[$field->name])) {
                        $formData[$field->name] = date('Y-m-d H:i:s');
                    }
                    break;
            }

            $data[$field->name] = $formData[$field->name];

            if ($field->unique === 1) {
                $uniqueField = $field;
                $uniqueKey = $formData[$field->name];
            }
        }
        unset($field);

        if ($uniqueField !== null) {
            if ($isNew) {
                $uniqueExist = Be::getTable('etl_material_item')
                        ->where('material_id', $materialId)
                        ->where('unique_key', $formData[$uniqueField->name])
                        ->getValue('COUNT(*)') > 0;
            } else {
                $uniqueExist = Be::getTable('etl_material_item')
                        ->where('material_id', $materialId)
                        ->where('unique_key', $formData[$uniqueField->name])
                        ->where('id', '!=', $materialItemId)
                        ->getValue('COUNT(*)') > 0;
            }

            if ($uniqueExist) {
                throw new ServiceException('素材内容' . $uniqueField->label . '（' . $formData[$uniqueField->name] . '）已存在！');
            }
        }

        $tuple->unique_key = $uniqueKey;
        $tuple->data = serialize($data);

    }


}
