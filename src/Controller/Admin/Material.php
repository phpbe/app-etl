<?php

namespace Be\App\Etl\Controller\Admin;

use Be\AdminPlugin\Detail\Item\DetailItemCode;
use Be\AdminPlugin\Form\Item\FormItemInputNumber;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * @BeMenuGroup("素材", icon="bi-journal-text", ordering="2")
 * @BePermissionGroup("素材", ordering="2")
 */
class Material extends Auth
{

    /**
     * 素材
     *
     * @BeMenu("素材", icon="bi-journal-bookmark", ordering="2.1")
     * @BePermission("素材", ordering="2.1")
     */
    public function materials()
    {
        Be::getAdminPlugin('Curd')->setting([

            'label' => '素材',
            'table' => 'etl_material',

            'grid' => [
                'title' => '素材',
                'orderBy' => 'create_time',
                'orderByDir' => 'DESC',

                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                    ],
                ],

                'titleRightToolbar' => [
                    'items' => [
                        [
                            'label' => '新建素材',
                            'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ],
                            'action' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'drawer' => [
                                'width' => '80%',
                            ],
                        ],
                    ]
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量删除',
                            'task' => 'delete',
                            'target' => 'ajax',
                            'confirm' => '确认要删除吗？',
                            'ui' => [
                                'icon' => 'el-icon-delete',
                                'type' => 'danger'
                            ]
                        ],
                    ]
                ],


                'table' => [

                    // 未指定时取表的所有字段
                    'items' => [
                        [
                            'driver' => TableItemSelection::class,
                            'width' => '50',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'driver' => TableItemLink::class,
                            'align' => 'left',
                            'task' => 'detail',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                        ],
                        [
                            'name' => 'field_count',
                            'label' => '字段数',
                            'align' => 'center',
                            'width' => '120',
                            'value' => function ($row) {
                                return count(unserialize($row['fields']));
                            },
                        ],
                        [
                            'name' => 'item_count',
                            'label' => '内容数',
                            'align' => 'center',
                            'width' => '120',
                            'driver' => TableItemLink::class,
                            'value' => function ($row) {
                                $sql = 'SELECT COUNT(*) FROM etl_material_item WHERE material_id = ?';
                                $count = Be::getDb()->getValue($sql, [$row['id']]);
                                return $count;
                            },
                            'action' => 'goMaterialItems',
                            'target' => 'self',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                    ],
                    'operation' => [
                        'label' => '操作',
                        'width' => '180',
                        'items' => [
                            [
                                'label' => '',
                                'tooltip' => '编辑',
                                'ui' => [
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-edit',
                                'action' => 'edit',
                                'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                                'drawer' => [
                                    'width' => '80%',
                                ],
                            ],
                            [
                                'label' => '',
                                'tooltip' => '删除',
                                'task' => 'delete',
                                'confirm' => '此操作为特理删除不可恢复，将删除素材及素材里的所有内容，确认要删除么？',
                                'target' => 'ajax',
                                'ui' => [
                                    'type' => 'danger',
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-delete',
                            ],
                        ]
                    ],
                ],
            ],

            'detail' => [
                'title' => '素材详情',
                'form' => [
                    'items' => [
                        [
                            'name' => 'id',
                            'label' => 'ID',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'fields',
                            'label' => '字段',
                            'driver' => DetailItemCode::class,
                            'language' => 'json',
                            'value' => function($row) {
                                return json_encode(unserialize($row['fields']), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
                            }
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                        ],
                    ]
                ],
            ],

            'delete' => [
                'events' => [
                    'before' => function ($tuple) {
                        Be::getTable('etl_material_item')
                            ->where('material_id', '=', $tuple->id)
                            ->delete();
                    },
                ],
            ]
        ])->execute();
    }


    /**
     * @BePermission("新建")
     */
    public function create()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $serviceMaterial = Be::getService('App.Etl.Admin.Material');
        if ($request->isAjax()) {

            try {
                $material = $serviceMaterial->create($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '新建素材成功！');
                $response->set('material', $material);
                $response->set('redirectUrl', beAdminUrl('Etl.Material.edit', ['id' => $material->id]));
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }

        } else {
            try {
                $response->set('title', '新建素材');
                $response->set('material', false);
                $response->display('App.Etl.Admin.Material.edit', 'Blank');
            } catch (\Exception $e) {
                $response->error($e->getMessage());
            }
        }
    }

    /**
     * @BePermission("编辑")
     */
    public function edit()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $serviceMaterial = Be::getService('App.Etl.Admin.Material');
        if ($request->isAjax()) {

            try {
                $material = $serviceMaterial->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '保存素材成功！');
                $response->set('material', $material);
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }

        } elseif ($request->isPost()) {

            $postData = $request->post('data', '', '');
            if ($postData) {
                $postData = json_decode($postData, true);
                if (isset($postData['row']['id']) && $postData['row']['id']) {
                    $response->redirect(beAdminUrl('Etl.Material.edit', ['id' => $postData['row']['id']]));
                }
            }

        } else {
            try {
                $materialId = $request->get('id', '');

                $material = $serviceMaterial->getMaterial($materialId);
                $response->set('material', $material);

                $response->set('title', '编辑素材');

                $response->display('App.Etl.Admin.Material.edit', 'Blank');
            } catch (\Exception $e) {
                $response->error($e->getMessage());
            }
        }
    }



    /**
     * 指定素材下的素材素材管理
     *
     * @BePermission("*")
     */
    public function goMaterialItems()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->post('data', '', '');
        if ($postData) {
            $postData = json_decode($postData, true);
            if (isset($postData['row']['id']) && $postData['row']['id']) {
                $response->redirect(beAdminUrl('Etl.MaterialItem.index', ['material_id' => $postData['row']['id']]));
            }
        }
    }

    /**
     * @BePermission("*")
     */
    public function getFields()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $postData = $request->json();

            $material = Be::getService('App.Etl.Admin.Material')->getMaterial($postData['materialId']);

            $fields = [];;
            $fields[] = 'id';
            $fields[] = 'material_id';
            $fields[] = 'unique_key';

            foreach ($material->fields as $field) {
                $fields[] = $field->name;
            }

            $fields[] = 'create_time';
            $fields[] = 'update_time';

            $response->set('success', true);
            $response->set('data', [
                'fields' => $fields,
            ]);
            $response->json();
        } catch (\Exception $e) {
            $response->set('success', false);
            $response->set('message', $e->getMessage());
            $response->json();
        }
    }


    /**
     * @BePermission("*")
     */
    public function getDataFields()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $postData = $request->json();

            $material = Be::getService('App.Etl.Admin.Material')->getMaterial($postData['materialId']);

            $fields = [];;
            foreach ($material->fields as $field) {
                $fields[] = $field->name;
            }

            $response->set('success', true);
            $response->set('data', [
                'fields' => $fields,
            ]);
            $response->json();
        } catch (\Exception $e) {
            $response->set('success', false);
            $response->set('message', $e->getMessage());
            $response->json();
        }
    }


}
