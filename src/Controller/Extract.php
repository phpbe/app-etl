<?php

namespace Be\App\Etl\Controller;

use Be\Plugin\Detail\Item\DetailItemCode;
use Be\Plugin\Detail\Item\DetailItemCodePhp;
use Be\Plugin\Detail\Item\DetailItemSwitch;
use Be\Plugin\Form\Item\FormItemDatePickerRange;
use Be\Plugin\Table\Item\TableItemLink;
use Be\Plugin\Toolbar\Item\ToolbarItemButtonDropDown;
use Be\Plugin\Form\Item\FormItemSelect;
use Be\Plugin\Table\Item\TableItemSelection;
use Be\Plugin\Table\Item\TableItemSwitch;
use Be\System\Be;
use Be\System\Request;
use Be\System\Controller;
use Be\System\Response;

/**
 * Class Extract
 * @package App\Etl\Controller
 *
 * @BeMenuGroup("抽取", icon="el-icon-fa fa-copy")
 * @BePermissionGroup("抽取")
 */
class Extract extends Controller
{
    /**
     * 任务管理
     *
     * @BeMenu("任务管理", icon="el-icon-fa fa-list-ul")
     * @BePermission("任务管理")
     */
    public function lists()
    {
        $dsKeyValues = Be::getService('Etl.Ds')->getIdNameKeyValues();
        $fieldMappingTypeKeyValues = Be::getService('Etl.Extract')->getFieldMappingTypeKeyValues();
        $breakpointTypeKeyValues = Be::getService('Etl.Extract')->getBreakpointTypeKeyValues();
        $breakpointStepKeyValues = Be::getService('Etl.Extract')->getBreakpointStepKeyValues();
        $categoryKeyValues = Be::getService('Etl.ExtractCategory')->getIdNameKeyValues();

        Be::getPlugin('Curd')->setting([

            'label' => '抽取任务管理',
            'table' => 'etl_extract',

            'lists' => [
                'title' => '抽取任务管理',

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
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'driver' => FormItemDatePickerRange::class,
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


                'toolbar' => [

                    'items' => [
                        [
                            'label' => '新建抽取任务',
                            'url' => beUrl('Etl.Extract.edit'),
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'drawer' => ['width' => '60%'],
                            'ui' => [
                                'button' => [
                                    'icon' => 'el-icon-plus',
                                    'type' => 'success',
                                ]
                            ]
                        ],
                        [
                            'label' => '批量启用',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '1',
                            ],
                            'target' => 'ajax',
                            'ui' => [
                                'button' => [
                                    'icon' => 'el-icon-fa fa-check',
                                    'type' => 'primary',
                                ]
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
                                'button' => [
                                    'icon' => 'el-icon-fa fa-lock',
                                    'type' => 'warning',
                                ]
                            ]
                        ],
                        [
                            'label' => '批量删除',
                            'task' => 'fieldEdit',
                            'target' => 'ajax',
                            'postData' => [
                                'field' => 'is_delete',
                                'value' => '1',
                            ],
                            'ui' => [
                                'button' => [
                                    'icon' => 'el-icon-delete',
                                    'type' => 'danger'
                                ]
                            ]
                        ],
                        [
                            'label' => '导出',
                            'driver' => ToolbarItemButtonDropDown::class,
                            'ui' => [
                                'button' => [
                                    'icon' => 'el-icon-fa fa-download',
                                ]
                            ],
                            'menus' => [
                                [
                                    'label' => 'CSV',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'csv',
                                    ],
                                    'target' => 'blank',
                                    'ui' => [
                                        'icon' => 'el-icon-fa fa-file-text-o',
                                    ],
                                ],
                                [
                                    'label' => 'EXCEL',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'excel',
                                    ],
                                    'target' => 'blank',
                                    'ui' => [
                                        'icon' => 'el-icon-fa fa-file-excel-o',
                                    ],
                                ],
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
                            'name' => 'id',
                            'label' => 'ID',
                            'width' => '60',
                        ],
                        [
                            'name' => 'name',
                            'label' => '任务名称',
                            'driver' => TableItemLink::class,
                            'align' => 'left',
                            'task' => 'detail',
                            'target' => 'drawer'
                        ],
                        [
                            'name' => 'src_ds_id',
                            'label' => '来源数据源',
                            'width' => '120',
                            'keyValues' => $dsKeyValues,
                        ],
                        [
                            'name' => 'src_table',
                            'label' => '来源数据源表名',
                            'width' => '120',
                        ],
                        [
                            'name' => 'dst_ds_id',
                            'label' => '目标数据源',
                            'width' => '120',
                            'keyValues' => $dsKeyValues,
                        ],
                        [
                            'name' => 'dst_table',
                            'label' => '目标数据源表名',
                            'width' => '120',
                        ],
                        [
                            'name' => 'breakpoint_type',
                            'label' => '断点类型',
                            'keyValues' => $breakpointTypeKeyValues,
                        ],
                        [
                            'name' => 'breakpoint_field',
                            'label' => '断点字段',
                        ],
                        [
                            'name' => 'breakpoint',
                            'label' => '断点',
                            'width' => '150',
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
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '150',
                        ],
                    ],
                    'exclude' => ['password', 'salt', 'remember_me_token']
                ],

                'operation' => [
                    'label' => '操作',
                    'width' => '160',
                    'items' => [
                        [
                            'label' => '编辑',
                            'url' => beUrl('Etl.Extract.edit'),
                            'target' => 'drawer',
                            'drawer' => ['width' => '60%'],
                            'ui' => [
                                'link' => [
                                    'type' => 'primary'
                                ]
                            ]
                        ],
                        [
                            'label' => '手动执行',
                            'url' => beUrl('Etl.Task.manualRunExtract'),
                            'target' => 'ajax',
                            'ui' => [
                                'link' => [
                                    'v-if' => 'scope.row.is_enable == \'1\'',
                                    'type' => 'warning'
                                ]
                            ]
                        ],
                        [
                            'label' => '删除',
                            'task' => 'fieldEdit',
                            'target' => 'ajax',
                            'postData' => [
                                'field' => 'is_delete',
                                'value' => 1,
                            ],
                            'ui' => [
                                'link' => [
                                    'type' => 'danger'
                                ]
                            ]
                        ],
                    ]
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
                            'label' => '来源数据源',
                            'keyValues' => $dsKeyValues,
                        ],
                        [
                            'name' => 'src_table',
                            'label' => '来源数据源表名',
                        ],
                        [
                            'name' => 'dst_ds_id',
                            'label' => '目标数据源',
                            'keyValues' => $dsKeyValues,
                        ],
                        [
                            'name' => 'dst_table',
                            'label' => '目标数据源表名',
                        ],
                        [
                            'name' => 'field_mapping_type',
                            'label' => '字段映射类型',
                            'keyValues' => $fieldMappingTypeKeyValues,
                        ],
                        [
                            'name' => 'field_mapping',
                            'label' => '映射数据',
                            'driver' => DetailItemCode::class,
                            'language' => 'php',
                        ],
                        [
                            'name' => 'breakpoint_type',
                            'label' => '断点类型',
                            'keyValues' => $breakpointTypeKeyValues,
                        ],
                        [
                            'name' => 'breakpoint_field',
                            'label' => '断点字段',
                        ],
                        [
                            'name' => 'breakpoint',
                            'label' => '断点',
                        ],
                        [
                            'name' => 'breakpoint_step',
                            'label' => '断点递增',
                            'keyValues' => $breakpointStepKeyValues,
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

    public function edit()
    {
        if (Request::isAjax()) {

            $db = Be::getDb();
            $db->startTransaction();
            try {
                $postData = Request::json();
                $formData = $postData['formData'];

                $tuple = Be::newTuple('etl_extract');
                if (isset($formData['id']) && $formData['id']) {
                    $tuple->load($formData['id']);
                } else {
                    $tuple->field_mapping_type = 0;
                    $tuple->field_mapping = '';
                    $tuple->breakpoint_type = 0;
                    $tuple->breakpoint_field = '';
                    $tuple->breakpoint = '';
                    $tuple->breakpoint_step = '';
                    $tuple->schedule = '';
                    $tuple->is_enable = '1';
                    $tuple->is_delete = '0';
                    $tuple->create_time = date('Y-m-d H:i:s');
                }

                if ($formData['step'] == '0') {
                    $tuple->name = $formData['name'];
                    $tuple->category_id = $formData['category_id'];
                    $tuple->src_ds_id = $formData['src_ds_id'];
                    $tuple->dst_ds_id = $formData['dst_ds_id'];
                    $tuple->src_table = $formData['src_table'];
                    $tuple->dst_table = $formData['dst_table'];
                } elseif  ($formData['step'] == '1') {
                    $tuple->field_mapping_type = $formData['field_mapping_type'];
                    $tuple->field_mapping = $formData['field_mapping'];
                } elseif ($formData['step'] == '2') {
                    $tuple->breakpoint_type = $formData['breakpoint_type'];
                    if ($tuple->breakpoint_type == '0') {
                        $tuple->breakpoint_field = '';
                        $tuple->breakpoint = '';
                        $tuple->breakpoint_step = '';
                    } else {
                        $tuple->breakpoint_field = $formData['breakpoint_field'];
                        $tuple->breakpoint = $formData['breakpoint'];
                        $tuple->breakpoint_step = $formData['breakpoint_step'];
                    }
                    $tuple->schedule = $formData['schedule'];
                }
                $tuple->update_time = date('Y-m-d H:i:s');
                $tuple->save();

                $db->commit();
                Response::set('success', true);
                Response::set('data', [
                    'extract' => $tuple,
                ]);
                Response::ajax();
            } catch (\Exception $e) {
                $db->rollback();
                Response::error($e->getMessage());
            }

        } else {

            try {
                $categoryKeyValues = Be::getService('Etl.ExtractCategory')->getIdNameKeyValues();
                Response::set('categoryKeyValues', $categoryKeyValues);

                $dsKeyValues = Be::getService('Etl.Ds')->getIdNameKeyValues();
                Response::set('dsKeyValues', (object)$dsKeyValues);

                $tuple = Be::newTuple('etl_extract');

                $postData = Request::post('data', '', '');
                if ($postData) {
                    $postData = json_decode($postData, true);
                    if (isset($postData['row']['id']) && $postData['row']['id']) {
                        $tuple->load($postData['row']['id']);
                    }
                }

                Response::set('extract', $tuple);

                $srcTableFields = [];
                $serviceDs = Be::getService('Etl.Ds');
                if ($tuple->src_ds_id > 0 && $tuple->src_table) {
                    $srcTableFields = $serviceDs->getTableFields($tuple->src_ds_id, $tuple->src_table);
                }
                Response::set('srcTableFields', $srcTableFields);

                $dstTableFields = [];
                if ($tuple->dst_ds_id > 0 && $tuple->dst_table) {
                    $dstTableFields = $serviceDs->getTableFields($tuple->dst_ds_id, $tuple->dst_table);
                }
                Response::set('dstTableFields', $dstTableFields);

                $fieldMappingTypeKeyValues = Be::getService('Etl.Extract')->getFieldMappingTypeKeyValues();
                Response::set('fieldMappingTypeKeyValues', (object)$fieldMappingTypeKeyValues);

                $breakpointTypeKeyValues = Be::getService('Etl.Extract')->getBreakpointTypeKeyValues();
                Response::set('breakpointTypeKeyValues', (object)$breakpointTypeKeyValues);

                $breakpointStepKeyValues = Be::getService('Etl.Extract')->getBreakpointStepKeyValues();
                Response::set('breakpointStepKeyValues', $breakpointStepKeyValues);

                Response::display('App.Etl.Extract.edit', 'Nude');
            } catch (\Exception $e) {
                Response::error($e->getMessage());
            }

        }
    }

}
