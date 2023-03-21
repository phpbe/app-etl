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
        $request = Be::getRequest();
        $response = Be::getResponse();

        $flowId = $request->get('flow_id', 'all');

        $flowKeyValues = Be::getService('App.Etl.Admin.Flow')->getIdNameKeyValues();
        $statusKeyValues = [
            'create' => '创建',
            'running' => '运行中',
            'finish' => '执行完成',
            'error' => '出错',
        ];

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
                            'keyValues' => \Be\Util\Arr::merge([
                                'all' => '全部',
                            ], $flowKeyValues),
                            'nullValue' => 'all',
                            'defaultValue' => 'all',
                            'value' => $flowId,
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'driver' => FormItemSelect::class,
                            'keyValues' => \Be\Util\Arr::merge([
                                'all' => '全部',
                            ], $statusKeyValues),
                            'nullValue' => 'all',
                            'defaultValue' => 'all',
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
                            'align' => 'left',
                            'value' => function ($row) {
                                $sql = 'SELECT name FROM etl_flow WHERE id = ?';
                                return \Be\Be::getDb()->getValue($sql, [$row['flow_id']]);
                            },
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'width' => '90',
                            'keyValues' => $statusKeyValues,
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
                            'value' => function ($row) {
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
                                'label' => '查看',
                                'url' => beAdminUrl('Etl.FlowLog.detail'),
                                'target' => 'drawer',
                                'drawer' => ['width' => '80%'],
                                'ui' => [
                                    'type' => 'primary',
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

                $flowLog = Be::getService('App.Etl.Admin.FlowLog')->getFlowLog($postData['row']['id']);
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
        $request = Be::getRequest();
        $response = Be::getResponse();

    }


    /**
     * 下载输出物
     *
     * @BePermission("运行记录")
     */
    public function downloadOutputFile()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $flowNodeLogId = $request->get('flow_node_log_id', '');

        $tuple = Be::getTuple('etl_flow_node_log');
        try {
            $tuple->load($flowNodeLogId);
        } catch (\Throwable $t) {
            throw new ControllerException('数据流节点日志（#' . $flowNodeLogId . '）不存在！');
        }

        if (!$tuple->output_file) {
            throw new ControllerException('数据流节点日志（#' . $flowNodeLogId . '）无有效输出物！');
        }

        $path = Be::getRuntime()->getRootPath() . $tuple->output_file;
        if (!file_exists($path)) {
            throw new ControllerException('输出物文件已删除！');
        }

        session_write_close();
        set_time_limit(3600);

        $response->header('Content-Type', 'application/octet stream');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Disposition', 'attachment; filename=etl-output-' . $flowNodeLogId . strrchr($tuple->output_file, '.'));
        $response->header('Pragma', 'no-cache');

        $f = fopen($path, 'r');
        while (!feof($f)) {
            $response->write(fread($f, 8192));
        }
        fclose($f);
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
