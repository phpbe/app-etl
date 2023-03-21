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
class FlowLog
{
    /**
     * 任务管理
     *
     * @BeMenu("运行记录", icon="bi-list", ordering="2.3")
     * @BePermission("运行记录")
     */
    public function index()
    {
        $flowKeyValues = Be::getService('App.Etl.Admin.Flow')->getIdNameKeyValues();

        Be::getAdminPlugin('Curd')->setting([

            'label' => '运行记录',
            'table' => 'etl_flow_log',

            'grid' => [
                'title' => '运行记录',
                'orderBy' => 'id',
                'orderByDir' => 'DESC',

                'form' => [
                    'items' => [
                        [
                            'name' => 'flow_id',
                            'label' => '数据流',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $flowKeyValues
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'driver' => FormItemSelect::class,
                            'keyValues' => [
                                '' => '不限',
                                'create' => '创建',
                                'running' => '运行中',
                                'finish' => '执行完成',
                                'error' => '出错',
                            ]
                        ],
                    ],
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量删除',
                            'action' => 'delete',
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
                            'name' => 'flow_name',
                            'label' => '数据流',
                            'algin' => 'left',
                            'target' => 'drawer',
                            'drawer' => ['width' => '80%'],
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'width' => '90',
                            'keyValues' => $flowKeyValues
                        ],
                        [
                            'name' => 'total',
                            'label' => '总数据',
                            'width' => '90',
                        ],
                        [
                            'name' => 'total_success',
                            'label' => '成功数',
                            'width' => '90',
                        ],
                        [
                            'name' => 'complete_time',
                            'label' => '完成时间',
                            'width' => '180',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '180',
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                            'width' => '180',
                        ],
                    ],

                    'operation' => [
                        'label' => '操作',
                        'width' => '240',
                        'items' => [
                            [
                                'label' => '运行记录',
                                'url' => beAdminUrl('Etl.FlowLog.index'),
                                'target' => 'blank',
                                'ui' => [
                                    'type' => 'info',
                                ]
                            ],
                            [
                                'label' => '删除',
                                'action' => 'delete',
                                'confirm' => '确认要删除么？',
                                'target' => 'ajax',
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


}
