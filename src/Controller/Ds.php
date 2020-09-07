<?php

namespace Be\App\Etl\Controller;


use Be\Plugin\Toolbar\Item\ToolbarItemButtonDropDown;
use Be\Plugin\Detail\Item\DetailItemSwitch;
use Be\Plugin\Form\Item\FormItemInputNumberInt;
use Be\Plugin\Form\Item\FormItemInputPassword;
use Be\Plugin\Form\Item\FormItemInputTextArea;
use Be\Plugin\Form\Item\FormItemSelect;
use Be\Plugin\Form\Item\FormItemSwitch;
use Be\Plugin\Table\Item\TableItemSelection;
use Be\Plugin\Table\Item\TableItemSwitch;
use Be\System\Be;
use Be\System\Db\Tuple;
use Be\System\Controller;
use Be\System\Request;
use Be\System\Response;

/**
 * Class Ds
 * @package App\Etl\Controller
 *
 * @BeMenuGroup("数据源", icon="el-icon-fa fa-database")
 * @BePermissionGroup("数据源")
 */
class Ds extends Controller
{
    /**
     * 数据源管理
     *
     * @BeMenu("数据源管理", icon="el-icon-fa fa-list-ul")
     * @BePermission("数据源管理")
     */
    public function lists()
    {
        $typeKeyValues = [
            'mysql' => 'Mysql',
            'oracle' => 'Oracel',
            'mssql' => 'SQL Server',
        ];

        Be::getPlugin('Curd')->setting([

            'label' => '数据源管理',
            'table' => 'etl_ds',

            'lists' => [
                'title' => '数据源列表',

                'filter' => [
                    ['is_delete', '=', '0'],
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'type',
                            'label' => '驱动类型',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $typeKeyValues
                        ],
                        [
                            'name' => 'db_host',
                            'label' => '主机名',
                        ],
                        [
                            'name' => 'db_name',
                            'label' => '库名',
                        ],
                    ],
                ],


                'toolbar' => [

                    'items' => [
                        [
                            'label' => '新建',
                            'task' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
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
                            'label' => '名称',
                        ],
                        [
                            'name' => 'type',
                            'label' => '驱动类型',
                            'width' => '100',
                            'keyValues' => $typeKeyValues
                        ],
                        [
                            'name' => 'db_host',
                            'label' => '主机名',
                        ],
                        [
                            'name' => 'db_port',
                            'label' => '端口号',
                            'width' => '90',
                        ],
                        [
                            'name' => 'db_user',
                            'label' => '用户名',
                            'width' => '100',
                        ],
                        [
                            'name' => 'db_name',
                            'label' => '库名',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '150',
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
                ],

                'operation' => [
                    'label' => '操作',
                    'width' => '120',
                    'items' => [
                        [
                            'label' => '查看',
                            'task' => 'detail',
                            'target' => 'drawer',
                            'ui' => [
                                'link' => [
                                    'type' => 'success'
                                ]
                            ]
                        ],
                        [
                            'label' => '编辑',
                            'task' => 'edit',
                            'target' => 'drawer',
                            'ui' => [
                                'link' => [
                                    'type' => 'primary'
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
                        [
                            'label' => 'ER',
                            'task' => 'er',
                            'target' => 'blank',
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
                            'label' => '名称',
                        ],
                        [
                            'name' => 'type',
                            'label' => '驱动类型',
                            'keyValues' => $typeKeyValues
                        ],
                        [
                            'name' => 'db_host',
                            'label' => '主机名',
                        ],
                        [
                            'name' => 'db_port',
                            'label' => '端口号',
                        ],
                        [
                            'name' => 'db_user',
                            'label' => '用户名',
                        ],
                        [
                            'name' => 'db_pass',
                            'label' => '密码',
                            'value' => function ($row) {
                                return str_repeat('*', strlen($row['db_pass']));
                            }
                        ],
                        [
                            'name' => 'db_name',
                            'label' => '库名',
                        ],
                        [
                            'name' => 'remark',
                            'label' => '备注',
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

            'create' => [
                'title' => '新建数据源',
                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'required' => true,
                        ],
                        [
                            'name' => 'type',
                            'label' => '驱动类型',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $typeKeyValues,
                            'required' => true,
                        ],
                        [
                            'name' => 'db_host',
                            'label' => '主机名',
                            'required' => true,
                            'unique' => true,
                        ],
                        [
                            'name' => 'db_port',
                            'label' => '端口号',
                            'driver' => FormItemInputNumberInt::class,
                            'required' => true,
                        ],
                        [
                            'name' => 'db_user',
                            'label' => '用户名',
                            'required' => true,
                        ],
                        [
                            'name' => 'db_pass',
                            'label' => '密码',
                            'driver' => FormItemInputPassword::class,
                            'required' => true,
                        ],
                        [
                            'name' => 'db_name',
                            'label' => '库名',
                            'required' => true,
                        ],
                        [
                            'name' => 'remark',
                            'label' => '备注',
                            'driver' => FormItemInputTextArea::class,
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'value' => 1,
                            'driver' => FormItemSwitch::class,
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        $tuple->create_time = date('Y-m-d H:i:s');
                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
                ],
            ],

            'edit' => [
                'title' => '编辑数据源',
                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'required' => true,
                        ],
                        [
                            'name' => 'type',
                            'label' => '驱动类型',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $typeKeyValues,
                            'required' => true,
                        ],
                        [
                            'name' => 'db_host',
                            'label' => '主机名',
                            'required' => true,
                        ],
                        [
                            'name' => 'db_port',
                            'label' => '端口号',
                            'driver' => FormItemInputNumberInt::class,
                            'required' => true,
                        ],
                        [
                            'name' => 'db_user',
                            'label' => '用户名',
                            'required' => true,
                        ],
                        [
                            'name' => 'db_pass',
                            'label' => '密码',
                            'driver' => FormItemInputPassword::class,
                            'required' => true,
                        ],
                        [
                            'name' => 'db_name',
                            'label' => '库名',
                            'required' => true,
                        ],
                        [
                            'name' => 'remark',
                            'label' => '备注',
                            'driver' => FormItemInputTextArea::class,
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'value' => 1,
                            'driver' => FormItemSwitch::class,
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        $tuple->update_time = date('Y-m-d H:i:s');
                    }
                ]
            ],

        ])->execute();
    }

    /**
     * @BePermission("*")
     */
    public function getTableNames()
    {
        try {
            $postData = Request::json();
            $tables = Be::getService('Etl.Ds')->getTableNames($postData['dsId']);
            Response::set('success', true);
            Response::set('data', [
                'tables' => $tables,
            ]);
            Response::ajax();
        } catch (\Exception $e) {
            Response::set('success', false);
            Response::set('message', $e->getMessage());
            Response::ajax();
        }
    }

}
