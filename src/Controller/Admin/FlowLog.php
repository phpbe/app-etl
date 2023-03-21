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
                'orderBy' => 'create_time',
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
                            'value' => function($row) {
                                $sql = 'SELECT name FROM etl_flow WHERE id = ?';
                                return \Be\Be::getDb()->getValue($sql, [$row['flow_id']]);
                            },
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
                            'name' => 'finish_time',
                            'label' => '完成时间',
                            'width' => '180',
                            'value' => function($row) {
                               if ($row['finish_time'] === '1970-01-02 00:00:00') {
                                   return '-';
                               } else {
                                   return $row['finish_time'];
                               }
                            },
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
                                'label' => '查看明细',
                                'url' => beAdminUrl('Etl.FlowLog.detail'),
                                'target' => 'drawer',
                                'drawer' => ['width' => '80%'],
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

        ])->execute();
    }



    /**
     * 运行记录
     *
     * @BePermission("运行记录")
     */
    public function detail()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->post('data', '', '');
        if ($postData) {
            $postData = json_decode($postData, true);
            if (isset($postData['row']['id']) && $postData['row']['id']) {

                $flowLog = Be::getService('App.Etl.Service.Admin.FlowLog')->getFlowLog($postData['row']['id']);
                $response->set('flowLog', $flowLog);

                $response->display(null, 'Blank');
            }
        }
    }


    /**
     * 运行记录
     *
     * @BePermission("运行记录")
     */
    public function delete()
    {

    }


    /**
     * 下载输出物
     *
     * @BePermission("运行记录")
     */
    public function downloadOutputFile()
    {

    }


    /**
     * 详细记录
     *
     * @BePermission("运行记录")
     */
    public function nodeItemLogs()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $flowNodeLogId = $request->get('flow_node_log_id', '');

        Be::getAdminPlugin('Curd')->setting([

            'label' => '运行详细记录',
            'table' => 'etl_flow_node_item_log',

            'grid' => [
                'title' => '运行详细记录',
                'orderBy' => 'create_time',
                'orderByDir' => 'DESC',
                'filter' => [
                    ['flow_node_log_id', '=', $flowNodeLogId],
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
                            'width' => '200',
                        ],
                        [
                            'name' => 'success',
                            'label' => '是否成功',
                            'width' => '90',
                        ],
                        [
                            'name' => 'message',
                            'label' => '消息',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '180',
                        ],
                    ],
                ],
            ],

        ])->execute();
    }


}
