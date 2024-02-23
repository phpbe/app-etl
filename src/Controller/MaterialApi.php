<?php

namespace Be\App\Etl\Controller;

use Be\App\ServiceException;
use Be\Be;

/**
 * 接口
 */
class MaterialApi
{

    /**
     * 创建
     *
     * @BeRoute("/etl/material/api/create")
     */
    public function create()
    {
        if (!$this->check()) return;

        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $materialId = $request->get('material_id');
            $format = $request->get('format', 'form');

            $material = Be::getService('App.Etl.Material')->getMaterial($materialId);

            $postData = [];
            $postData['material_id'] = $materialId;
            foreach ($material->fields as $field) {
                switch ($field->type) {
                    case 'text':
                    case 'textarea':
                    case 'date':
                    case 'datetime':
                        if ($format === 'form') {
                            $postData[$field->name] = $request->post($field->name, '');
                        } elseif ($format === 'json') {
                            $postData[$field->name] = $request->json($field->name, '');
                        }
                        break;
                    case 'html':
                        if ($format === 'form') {
                            $postData[$field->name] = $request->post($field->name, '', 'html');
                        } elseif ($format === 'json') {
                            $postData[$field->name] = $request->json($field->name, '', 'html');
                        }
                        break;
                    case 'int':
                    case 'bool':
                        if ($format === 'form') {
                            $postData[$field->name] = $request->post($field->name, '', 'int');
                        } elseif ($format === 'json') {
                            $postData[$field->name] = $request->json($field->name, '', 'int');
                        }
                        break;
                    case 'float':
                        if ($format === 'form') {
                            $postData[$field->name] = $request->post($field->name, '', 'float');
                        } elseif ($format === 'json') {
                            $postData[$field->name] = $request->json($field->name, '', 'float');
                        }
                        break;
                }
            }

            Be::getService('App.Etl.Admin.MaterialItem')->edit($postData);

            $response->set('success', true);
            $response->set('message', '[OK] 新增素材内容成功！');
            $response->json();

        } catch (\Throwable $t) {

            $response->set('success', false);
            $response->set('message', '[ERROR] 新增素材内容失败：' . $t->getMessage());
            $response->json();
        }
    }

    /**
     * 编辑
     *
     * @BeRoute("/etl/material/api/edit")
     */
    public function edit()
    {
        if (!$this->check()) return;

        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $materialId = $request->get('material_id');
            $format = $request->get('format', 'form');

            $material = Be::getService('App.Etl.Material')->getMaterial($materialId);

            $postData = [];
            $postData['material_id'] = $materialId;
            foreach ($material->fields as $field) {
                switch ($field->type) {
                    case 'text':
                    case 'textarea':
                    case 'date':
                    case 'datetime':
                        if ($format === 'form') {
                            $postData[$field->name] = $request->post($field->name, '');
                        } elseif ($format === 'json') {
                            $postData[$field->name] = $request->json($field->name, '');
                        }
                        break;
                    case 'html':
                        if ($format === 'form') {
                            $postData[$field->name] = $request->post($field->name, '', 'html');
                        } elseif ($format === 'json') {
                            $postData[$field->name] = $request->json($field->name, '', 'html');
                        }
                        break;
                    case 'int':
                    case 'bool':
                        if ($format === 'form') {
                            $postData[$field->name] = $request->post($field->name, '', 'int');
                        } elseif ($format === 'json') {
                            $postData[$field->name] = $request->json($field->name, '', 'int');
                        }
                        break;
                    case 'float':
                        if ($format === 'form') {
                            $postData[$field->name] = $request->post($field->name, '', 'float');
                        } elseif ($format === 'json') {
                            $postData[$field->name] = $request->json($field->name, '', 'float');
                        }
                        break;
                }
            }

            // 有唯一键时，检查唯一键是否需要编辑
            $uniqueField = null;
            foreach ($material->fields as $field) {
                if ($field->unique === 1) {
                    $uniqueField = $field;
                    break;
                }
            }

            if ($uniqueField !== null) {
                $uniqueKey = $postData[$uniqueField->name];
                $sql = 'SELECT id FROM etl_material_item WHERE unique_key=?';
                $id = Be::getDb()->getValue($sql, [$uniqueKey]);
                if ($id) {
                    $postData['id'] = $id;
                }
            }

            Be::getService('App.Etl.Admin.MaterialItem')->edit($postData);

            $response->set('success', true);
            $response->set('message', '[OK] 新增/更新素材内容成功！');
            $response->json();

        } catch (\Throwable $t) {

            $response->set('success', false);
            $response->set('message', '[ERROR] 新增/更新素材内容失败：' . $t->getMessage());
            $response->json();
        }
    }

    /**
     * 取用
     *
     * @BeRoute("/etl/material/api/fetch")
     */
    public function fetch()
    {
        if (!$this->check()) return;

        $request = Be::getRequest();
        $response = Be::getResponse();

        try {

            $materialId = $request->get('material_id');

            $material = Be::getService('App.Etl.Material')->getMaterial($materialId);

            $page = $request->request('page', 1, 'int');
            if ($page < 0) $page = 1;

            $pageSize = $request->request('pageSize', 100, 'int');
            if ($pageSize < 0) $pageSize = 1;
            if ($pageSize > 5000) $pageSize = 5000;

            $orderBy = $request->request('orderBy', 'create_time');
            if (!in_array($orderBy, ['create_time', 'update_time'])) {
                $orderBy = 'create_time';
            }

            $orderByDir = $request->request('orderByDir', 'asc');
            if (!in_array($orderByDir, ['asc', 'desc'])) {
                $orderByDir = 'asc';
            }

            $table = Be::getTable('etl_material_item');
            $table->where('material_id', $request->get('material_id'));

            $total = $table->count();
            $pages = (int)ceil($total / $pageSize);

            if ($page > $pages) $page = $pages;

            $table->orderBy($orderBy, $orderByDir);

            $table->limit($pageSize);
            $table->offset(($page - 1) * $pageSize);

            $rows = $table->getObjects();

            $formattedRows = [];
            foreach ($rows as $row) {

                $formattedRow = new \stdClass();
                $formattedRow->id = $row->id;
                $formattedRow->material_id = $row->material_id;
                $formattedRow->unique_key = $row->unique_key;

                $data = unserialize($row->data);
                foreach ($material->fields as $field) {
                    $fieldName = $field->name;
                    if (isset($data[$fieldName])) {
                        $formattedRow->$fieldName = $data[$fieldName];
                    } else {
                        $formattedRow->$fieldName = $field->default;
                    }
                }

                $formattedRow->create_time = $row->create_time;
                $formattedRow->update_time = $row->update_time;

                $formattedRows[] = $formattedRow;
            }

            $response->set('success', true);
            $response->set('message', '[OK] 取用素材内容成功！');
            $response->set('page', $page);
            $response->set('pageSize', $pageSize);
            $response->set('orderBy', $orderBy);
            $response->set('orderByDir', $orderByDir);
            $response->set('total', $total);
            $response->set('pages', $pages);
            $response->set('rows', $formattedRows);

            $response->json();

        } catch (\Throwable $t) {

            $response->set('success', false);
            $response->set('message', '[ERROR] 取用素材内容失败：' . $t->getMessage());
            $response->json();

        }
    }


    /**
     * 检查权限及参数
     *
     * @return bool
     */
    private function check()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $serviceMaterialApi = Be::getService('App.Etl.Admin.MaterialApi');
        $materialApiConfig = $serviceMaterialApi->getConfig();

        if ($materialApiConfig->enable === 0) {
            $response->set('success', false);
            $response->set('message', '素材 API 接口未启用！');
            $response->json();
            return false;
        }

        $token = $request->get('token', '');
        if ($materialApiConfig->token !== $token) {
            $response->set('success', false);
            $response->set('message', '素材 API Token 无效！');
            $response->json();
            return false;
        }

        $materialId = $request->get('material_id', '');
        if ($materialId === '') {
            $response->set('success', false);
            $response->set('message', '参数素材ID（material_id）缺失！');
            $response->json();
            return false;
        }

        return true;
    }


}
