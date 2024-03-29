<?php

namespace Be\App\Etl\Controller\Admin;

use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\Be;
use Be\App\ControllerException;
use Be\AdminPlugin\Detail\Item\DetailItemCode;
use Be\AdminPlugin\Detail\Item\DetailItemSwitch;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Table\Item\TableItemSwitch;

/**
 * Class Extract
 * @package Be\App\Etl\Controller\Admin
 *
 * @BeMenuGroup("数据流")
 * @BePermissionGroup("数据流")
 */
class Flow
{
    /**
     * 任务管理
     *
     * @BeMenu("数据流", icon="bi-arrow-left-right", ordering="3.2")
     * @BePermission("数据流")
     */
    public function index()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $categoryKeyValues = Be::getService('App.Etl.Admin.FlowCategory')->getIdNameKeyValues();

        $cookieKey = 'App-Etl-Admin-Flow-index-categoryId';

        $categoryId = false;
        if ($request->isGet()) {
            $categoryId = $request->cookie($cookieKey, false);
        } else {
            if ($request->isPost()) {
                $categoryId = $request->json('formData.category_id', false);
                if ($categoryId !== false) {
                    $response->cookie($cookieKey, $categoryId, time() + 30 * 86400);
                }
            }
        }

        if ($categoryId === false) {
            $categoryId = key($categoryKeyValues);
        }

        Be::getAdminPlugin('Curd')->setting([

            'label' => '数据流',
            'table' => 'etl_flow',

            'grid' => [
                'title' => '数据流',
                'orderBy' => 'create_time',
                'orderByDir' => 'DESC',
                'filter' => [
                    ['is_delete', '=', '0'],
                ],

                'tab' => [
                    'name' => 'category_id',
                    'keyValues' => $categoryKeyValues,
                    'value' => $categoryId,
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用状态',
                            'driver' => FormItemSelect::class,
                            'keyValues' => [
                                '' => '不限',
                                '1' => '启用',
                                '0' => '禁用',
                            ]
                        ],
                    ],
                ],

                'titleToolbar' => [
                    'items' => [
                        [
                            'label' => '导出',
                            'driver' => ToolbarItemDropDown::class,
                            'ui' => [
                                'icon' => 'el-icon-download',
                            ],
                            'menus' => [
                                [
                                    'label' => 'CSV',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'csv',
                                    ],
                                    'target' => 'blank',
                                ],
                                [
                                    'label' => 'EXCEL',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'excel',
                                    ],
                                    'target' => 'blank',
                                ],
                            ]
                        ],
                    ]
                ],

                'titleRightToolbar' => [
                    'items' => [
                        [
                            'label' => '新建',
                            'url' => beAdminUrl('Etl.Flow.create'),
                            'target' => 'self', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ]
                        ],
                    ]
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量启用',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '1',
                            ],
                            'target' => 'ajax',
                            'ui' => [
                                'icon' => 'el-icon-check',
                                'type' => 'success',
                            ]
                        ],
                        [
                            'label' => '批量禁用',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '0',
                            ],
                            'target' => 'ajax',
                            'ui' => [
                                'icon' => 'el-icon-close',
                                'type' => 'warning',
                            ]
                        ],
                        [
                            'label' => '批量删除',
                            'task' => 'fieldEdit',
                            'target' => 'ajax',
                            'confirm' => '确认要删除吗？',
                            'postData' => [
                                'field' => 'is_delete',
                                'value' => '1',
                            ],
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
                            'label' => '任务名称',
                            'driver' => TableItemLink::class,
                            'align' => 'left',
                            'task' => 'detail',
                            'target' => 'drawer',
                            'drawer' => ['width' => '80%'],
                        ],
                        [
                            'name' => 'node_qty',
                            'label' => '节点数',
                            'width' => '90',
                            'value' => function($row) {
                                $sql = 'SELECT COUNT(*) FROM etl_flow_node WHERE flow_id = ?';
                                return \Be\Be::getDb()->getValue($sql, [$row['id']]);
                            },
                        ],
                        [
                            'name' => 'schedule',
                            'label' => '执行计划',
                            'width' => '90',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'driver' => TableItemSwitch::class,
                            'target' => 'ajax',
                            'task' => 'fieldEdit',
                            'width' => '90',
                            'exportValue' => function ($row) {
                                return $row['is_enable'] ? '启用' : '禁用';
                            },
                        ],
                    ],


                    'operation' => [
                        'label' => '操作',
                        'width' => '240',
                        'items' => [
                            [
                                'label' => '编辑',
                                'url' => beAdminUrl('Etl.Flow.edit'),
                                'target' => 'self',
                                'ui' => [
                                    'type' => 'primary',
                                ]
                            ],
                            [
                                'label' => '运行记录',
                                'action' => 'goFlowLogs',
                                'target' => 'self',
                                'ui' => [
                                    'type' => 'success',
                                ]
                            ],
                            [
                                'label' => '删除',
                                'task' => 'fieldEdit',
                                'confirm' => '确认要删除么？',
                                'target' => 'ajax',
                                'postData' => [
                                    'field' => 'is_delete',
                                    'value' => 1,
                                ],
                                'ui' => [
                                    'type' => 'danger',
                                ]
                            ],
                        ]
                    ],
                ],
            ],

            'detail' => [
                'form' => [
                    'items' => [
                        [
                            'name' => 'id',
                            'label' => 'ID',
                        ],
                        [
                            'name' => 'name',
                            'label' => '任务名称',
                        ],
                        [
                            'name' => 'node_qty',
                            'label' => '节点数',
                            'width' => '90',
                            'value' => function($row) {
                                $sql = 'SELECT COUNT(*) FROM etl_flow_node WHERE flow_id = ?';
                                return \Be\Be::getDb()->getValue($sql, [$row['id']]);
                            },
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'driver' => DetailItemSwitch::class,
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

        ])->execute();
    }

    /**
     * @BePermission("新建")
     */
    public function create()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $serviceFlow = Be::getService('App.Etl.Admin.Flow');
        if ($request->isAjax()) {

            try {
                $flow = $serviceFlow->create($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '新建数据流成功！');
                $response->set('flow', $flow);
                $response->set('redirectUrl', beAdminUrl('Etl.Flow.edit', ['id' => $flow->id]));
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }

        } else {
            try {
                $categoryKeyValues = Be::getService('App.Etl.Admin.FlowCategory')->getIdNameKeyValues();
                $response->set('categoryKeyValues', $categoryKeyValues);
                $response->set('title', '新建数据流');
                $response->display();
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

        $serviceFlow = Be::getService('App.Etl.Admin.Flow');
        if ($request->isAjax()) {

            try {
                $flow = $serviceFlow->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '保存数据流成功！');
                $response->set('flow', $flow);
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
                    $response->redirect(beAdminUrl('Etl.Flow.edit', ['id' => $postData['row']['id']]));
                }
            }

        } else {

            try {

                $flowId = $request->get('id', '');

                $flow = $serviceFlow->getFlow($flowId);
                $response->set('flow', $flow);

                $categoryKeyValues = Be::getService('App.Etl.Admin.FlowCategory')->getIdNameKeyValues();
                $response->set('categoryKeyValues', $categoryKeyValues);

                $dsKeyValues = Be::getService('App.Etl.Admin.Ds')->getIdNameKeyValues();
                $response->set('dsKeyValues', $dsKeyValues);

                $dsTypeKeyValues = $serviceFlow->getDsTypeKeyValues();
                $response->set('dsTypeKeyValues', $dsTypeKeyValues);

                $breakpointKeyValues = $serviceFlow->getBreakpointKeyValues();
                $response->set('breakpointKeyValues', $breakpointKeyValues);

                $breakpointStepKeyValues = $serviceFlow->getBreakpointStepKeyValues();
                $response->set('breakpointStepKeyValues', $breakpointStepKeyValues);

                $fieldMappingKeyValues = $serviceFlow->getFieldMappingKeyValues();
                $response->set('fieldMappingKeyValues', $fieldMappingKeyValues);

                $serviceMaterial = Be::getService('App.Etl.Admin.Material');
                $materialKeyValues = $serviceMaterial->getIdNameKeyValues();
                $response->set('materialKeyValues', $materialKeyValues);

                $response->set('title', '编辑数据流');

                $response->display();
            } catch (\Exception $e) {
                $response->error($e->getMessage());
            }

        }
    }

    /**
     * @BePermission("验证")
     */
    public function test()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $index = $request->json('index', 0, 'int');

            $serviceFlow = Be::getService('App.Etl.Admin.Flow');
            $flow = $serviceFlow->test($request->json('formData'), $index);
            $response->set('success', true);
            $response->set('message', '验证数据流成功！');
            $response->set('flow', $flow);
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }


    /**
     * 跳转到运行记录页面
     *
     * @BePermission("*")
     */
    public function goFlowLogs()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->post('data', '', '');
        if ($postData) {
            $postData = json_decode($postData, true);
            if (isset($postData['row']['id']) && $postData['row']['id']) {
                $response->redirect(beAdminUrl('Etl.FlowLog.index', ['flow_id' => $postData['row']['id']]));
            }
        }
    }

}
