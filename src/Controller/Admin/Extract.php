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
 * BeMenuGroup("抽取", icon="bi-box-arrow-right", ordering="2")
 * @BePermissionGroup("抽取", ordering="2.1")
 */
class Extract
{
    /**
     * 任务管理
     *
     * BeMenu("任务管理", icon="bi-list", ordering="2.1")
     * @BePermission("任务管理")
     */
    public function lists()
    {
        $dsKeyValues = Be::getService('App.Etl.Admin.Ds')->getIdNameKeyValues();
        $fieldMappingTypeKeyValues = Be::getService('App.Etl.Admin.Extract')->getFieldMappingTypeKeyValues();
        $breakpointTypeKeyValues = Be::getService('App.Etl.Admin.Extract')->getBreakpointTypeKeyValues();
        $breakpointStepKeyValues = Be::getService('App.Etl.Admin.Extract')->getBreakpointStepKeyValues();
        $categoryKeyValues = Be::getService('App.Etl.Admin.ExtractCategory')->getIdNameKeyValues();

        Be::getAdminPlugin('Curd')->setting([

            'label' => '抽取任务管理',
            'table' => 'etl_extract',

            'grid' => [
                'title' => '抽取任务管理',
                'orderBy' => 'id',
                'orderByDir' => 'DESC',
                'filter' => [
                    ['is_delete', '=', '0'],
                ],

                'tab' => [
                    'name' => 'category_id',
                    'keyValues' => $categoryKeyValues,
                    'value' => key($categoryKeyValues),
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '任务名称',
                        ],
                        [
                            'name' => 'breakpoint_type',
                            'label' => '断点类型',
                            'driver' => FormItemSelect::class,
                            'keyValues' => array_merge(['' => '不限'], $breakpointTypeKeyValues)
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
                            'url' => beAdminUrl('Etl.Extract.edit'),
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'drawer' => ['width' => '80%'],
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
                            'name' => 'src',
                            'label' => '输入',
                            'width' => '120',
                            'value' => function ($row) use ($dsKeyValues) {
                                $ds = isset($dsKeyValues[$row['src_ds_id']]) ? $dsKeyValues[$row['src_ds_id']] : '';
                                if ($row['src_type'] == 'sql') {
                                    $table = 'SQL语句';
                                } else {
                                    $table = $row['src_table'];
                                }
                                return $ds . '.' . $table;
                            }
                        ],
                        [
                            'name' => 'dst',
                            'label' => '输出',
                            'width' => '120',
                            'value' => function ($row) use ($dsKeyValues) {
                                $ds = isset($dsKeyValues[$row['dst_ds_id']]) ? $dsKeyValues[$row['dst_ds_id']] : '';
                                $table = $row['dst_table'];
                                return $ds . '.' . $table;
                            }
                        ],
                        [
                            'name' => 'breakpoint',
                            'label' => '断点',
                            'value' => function ($row) {
                                if ($row['breakpoint_type'] === 'breakpoint') {
                                    return $row['breakpoint_field'] . '=' . $row['breakpoint'];
                                } else {
                                    return '全量';
                                }
                            }
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
                        'width' => '220',
                        'items' => [
                            [
                                'label' => '编辑',
                                'url' => beAdminUrl('Etl.Extract.edit'),
                                'target' => 'drawer',
                                'drawer' => ['width' => '80%'],
                                'ui' => [
                                    'type' => 'primary',
                                ]
                            ],
                            [
                                'label' => '手动执行',
                                'url' => beAdminUrl('Etl.Task.manualRunExtract'),
                                'target' => 'ajax',
                                'ui' => [
                                    'v-if' => 'scope.row.is_enable == \'1\'',
                                    'type' => 'success',
                                ]
                            ],
                            [
                                'label' => '日志',
                                'action' => 'log',
                                'target' => 'blank',
                                'ui' => [
                                    'type' => 'info',
                                ]
                            ],
                            [
                                'label' => '异常',
                                'action' => 'exception',
                                'target' => 'blank',
                                'ui' => [
                                    'type' => 'warning',
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
                            'name' => 'src_ds_id',
                            'label' => '输入数据源',
                            'keyValues' => $dsKeyValues,
                        ],
                        [
                            'name' => 'src_table',
                            'label' => '输入数据源表名',
                        ],
                        [
                            'name' => 'dst_ds_id',
                            'label' => '输出数据源',
                            'keyValues' => $dsKeyValues,
                        ],
                        [
                            'name' => 'dst_table',
                            'label' => '输出数据源表名',
                        ],
                        [
                            'name' => 'field_mapping_type',
                            'label' => '字段映射类型',
                            'keyValues' => $fieldMappingTypeKeyValues,
                        ],
                        [
                            'name' => 'field_mapping',
                            'label' => '字段映射',
                            'ui' => [
                                'form-item' => [
                                    'v-if' => 'formData.field_mapping_type == \'mapping\'',
                                ]
                            ]
                        ],
                        [
                            'name' => 'field_mapping_code',
                            'label' => '代码处理',
                            'driver' => DetailItemCode::class,
                            'language' => 'php',
                            'ui' => [
                                'form-item' => [
                                    'v-show' => 'formData.field_mapping_type == \'code\'',
                                ]
                            ]
                        ],
                        [
                            'name' => 'breakpoint_type',
                            'label' => '断点类型',
                            'keyValues' => $breakpointTypeKeyValues,
                        ],
                        [
                            'name' => 'breakpoint_field',
                            'label' => '断点字段',
                            'ui' => [
                                'form-item' => [
                                    'v-if' => 'formData.breakpoint_type == \'breakpoint\'',
                                ]
                            ],
                        ],
                        [
                            'name' => 'breakpoint',
                            'label' => '断点',
                            'ui' => [
                                'form-item' => [
                                    'v-if' => 'formData.breakpoint_type == \'breakpoint\'',
                                ]
                            ],
                        ],
                        [
                            'name' => 'breakpoint_step',
                            'label' => '断点递增',
                            'keyValues' => $breakpointStepKeyValues,
                            'ui' => [
                                'form-item' => [
                                    'v-if' => 'formData.breakpoint_type == \'breakpoint\'',
                                ]
                            ],
                        ],
                        [
                            'name' => 'breakpoint_offset',
                            'label' => '断点向前编移量',
                            'ui' => [
                                'form-item' => [
                                    'v-if' => 'formData.breakpoint_type == \'breakpoint\'',
                                ]
                            ],
                        ],
                        [
                            'name' => 'schedule',
                            'label' => '执行计划',
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
     * @BePermission("任务日志")
     */
    public function log()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->post('data', '', '');
        $postData = json_decode($postData, true);
        if (!isset($postData['row']['id'])) {
            throw new ControllerException('主键（row.id）缺失！');
        }
        $response->redirect(beAdminUrl('Etl.ExtractLog.lists', ['extractId' => $postData['row']['id']]));
    }

    /**
     * @BePermission("任务异常")
     */
    public function exception()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->post('data', '', '');
        $postData = json_decode($postData, true);
        if (!isset($postData['row']['id'])) {
            throw new ControllerException('主键（row.id）缺失！');
        }
        $response->redirect(beAdminUrl('Etl.ExtractException.lists', ['extractId' => $postData['row']['id']]));
    }

    /**
     * @BePermission("编辑")
     */
    public function edit()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {

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
                $response->set('data', [
                    'extract' => $tuple,
                ]);
                $response->json();
            } catch (\Exception $e) {
                $db->rollback();
                $response->error($e->getMessage());
            }

        } else {

            try {
                $categoryKeyValues = Be::getService('App.Etl.Admin.ExtractCategory')->getIdNameKeyValues();
                $response->set('categoryKeyValues', $categoryKeyValues);

                $dsKeyValues = Be::getService('App.Etl.Admin.Ds')->getIdNameKeyValues();
                $response->set('dsKeyValues', (object)$dsKeyValues);

                $srcTypeKeyValues = Be::getService('App.Etl.Admin.Extract')->getSrcTypeKeyValues();
                $response->set('srcTypeKeyValues', (object)$srcTypeKeyValues);

                $extract = false;
                $postData = $request->post('data', '', '');
                if ($postData) {
                    $postData = json_decode($postData, true);
                    if (isset($postData['row']['id']) && $postData['row']['id']) {
                        $tuple = Be::getTuple('etl_extract');
                        $tuple->load($postData['row']['id']);
                        $extract = $tuple->toObject();
                    }
                }

                $response->set('extract', $extract);

                $fieldMappingTypeKeyValues = Be::getService('App.Etl.Admin.Extract')->getFieldMappingTypeKeyValues();
                $response->set('fieldMappingTypeKeyValues', (object)$fieldMappingTypeKeyValues);

                $breakpointTypeKeyValues = Be::getService('App.Etl.Admin.Extract')->getBreakpointTypeKeyValues();
                $response->set('breakpointTypeKeyValues', (object)$breakpointTypeKeyValues);

                $breakpointStepKeyValues = Be::getService('App.Etl.Admin.Extract')->getBreakpointStepKeyValues();
                $response->set('breakpointStepKeyValues', $breakpointStepKeyValues);

                $response->display('App.Etl.Extract.edit', 'Blank');
            } catch (\Exception $e) {
                $response->error($e->getMessage());
            }

        }
    }

}
