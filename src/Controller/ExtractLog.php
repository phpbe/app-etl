<?php

namespace Be\App\Etl\Controller;

use Be\Plugin\Detail\Item\DetailItemCode;
use Be\Plugin\Detail\Item\DetailItemProgress;
use Be\Plugin\Form\Item\FormItemDatePickerRange;
use Be\Plugin\Form\Item\FormItemSelect;
use Be\Plugin\Table\Item\TableItemProgress;
use Be\System\Be;
use Be\System\Controller;
use Be\System\Request;

/**
 * Class ExtractLog
 * @package App\Etl\Controller
 *
 * @BePermissionGroup("抽取")
 */
class ExtractLog extends Controller
{

    /**
     * 任务日志
     *
     * @BePermission("任务日志")
     */
    public function lists()
    {
        $extractId = Request::get('extractId');

        $statusKeyValues = Be::getService('Etl.ExtractLog')->getStatusKeyValues();
        $triggerKeyValues = Be::getService('Etl.ExtractLog')->getTriggerKeyValues();
        $breakpointTypeKeyValues = Be::getService('Etl.Extract')->getBreakpointTypeKeyValues();
        $breakpointStepKeyValues = Be::getService('Etl.Extract')->getBreakpointStepKeyValues();

        Be::getPlugin('Curd')->setting([

            'label' => '任务日志',
            'table' => 'etl_extract_log',

            'lists' => [
                'title' => '任务日志',
                'reload' => '10', // 10 秒刷新下数据
                'orderBy' => 'id',
                'orderByDir' => 'DESC',
                'filter' => [
                    ['extract_id', '=', $extractId],
                ],
                'form' => [
                    'items' => [
                        [
                            'name' => 'create_time',
                            'label' => '时间',
                            'driver' => FormItemDatePickerRange::class,
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $statusKeyValues,
                        ],
                    ],
                ],

                'table' => [
                    'items' => [
                        [
                            'name' => 'id',
                            'label' => 'ID',
                            'width' => '60',
                        ],
                        [
                            'name' => 'breakpoint_type',
                            'label' => '断点类型',
                            'keyValues' => $breakpointTypeKeyValues,
                            'width' => '90',
                        ],
                        [
                            'name' => 'breakpoint',
                            'label' => '断点',
                            'width' => '160',
                            'value' => function ($row) {
                                if ($row['breakpoint_type'] == 1) {
                                    return $row['breakpoint'];
                                }
                                return '-';
                            },

                        ],
                        [
                            'name' => 'breakpoint_step',
                            'label' => '断点递增量',
                            'keyValues' => $breakpointStepKeyValues,
                            'width' => '90',
                            'value' => function ($row) {
                                if ($row['breakpoint_type'] == 1) {
                                    return $row['breakpoint_step'];
                                }
                                return '-';
                            },
                        ],
                        [
                            'name' => 'total',
                            'label' => '总数据量',
                            'width' => '90',
                        ],
                        [
                            'name' => 'offset',
                            'label' => '已处理数据量',
                            'width' => '100',
                        ],
                        [
                            'name' => 'progress',
                            'label' => '进度',
                            'value' => function ($row) {
                                if ($row['total'] == 0) {
                                    return 100;
                                }
                                return round($row['offset'] * 100 / $row['total'], 1);
                            },
                            'driver' => TableItemProgress::class,
                            'width' => '120',
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'keyValues' => $statusKeyValues,
                            'width' => '90',
                        ],
                        [
                            'name' => 'message',
                            'label' => '异常信息',
                        ],
                        [
                            'name' => 'trigger',
                            'label' => '触发方式',
                            'keyValues' => $triggerKeyValues,
                            'width' => '90',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '160',
                        ],
                        [
                            'name' => 'complete_time',
                            'label' => '完成时间',
                            'value' => function ($row) {
                                if ($row['status'] == 2) {
                                    return $row['complete_time'];
                                }
                                return '-';
                            },
                            'width' => '160',
                        ],
                    ],
                ],

                'operation' => [
                    'label' => '操作',
                    'width' => '150',
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
                            'label' => '快照信息',
                            'action' => 'snapshot',
                            'target' => 'drawer',
                            'drawer' => [
                                'width' => '60%',
                            ],
                            'ui' => [
                                'link' => [
                                    'type' => 'primary'
                                ]
                            ]
                        ],
                        [
                            'label' => '删除',
                            'task' => 'delete',
                            'confirm' => '确认要删除么？',
                            'target' => 'ajax',
                            'ui' => [
                                'link' => [
                                    'type' => 'danger'
                                ]
                            ]
                        ]
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
                            'name' => 'breakpoint_type',
                            'label' => '断点类型',
                            'keyValues' => $breakpointTypeKeyValues,
                        ],
                        [
                            'name' => 'breakpoint',
                            'label' => '断点',
                        ],
                        [
                            'name' => 'breakpoint_step',
                            'label' => '断点递增量',
                        ],
                        [
                            'name' => 'total',
                            'label' => '总数据量',
                        ],
                        [
                            'name' => 'offset',
                            'label' => '已处理数据量',
                        ],
                        [
                            'name' => 'progress',
                            'label' => '进度',
                            'value' => function ($row) {
                                if ($row['total'] == 0) {
                                    return 100;
                                }
                                return $row['offset'] * 100 / $row['total'];
                            },
                            'driver' => DetailItemProgress::class,
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'keyValues' => $statusKeyValues,
                        ],
                        [
                            'name' => 'message',
                            'label' => '异常信息',
                        ],
                        [
                            'name' => 'trigger',
                            'label' => '触发方式',
                            'keyValues' => $triggerKeyValues,
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                        ],
                        [
                            'name' => 'complete_time',
                            'label' => '完成时间',
                            'value' => function ($row) {
                                if ($row['status'] == 2) {
                                    return $row['complete_time'];
                                }
                                return '-';
                            },
                        ],
                    ]
                ],
            ],

        ])->execute();
    }

    /**
     * 任务日志
     *
     * @BePermission("任务快照")
     */
    public function snapshot()
    {

        $postData = Request::post('data', '', '');
        if ($postData) {
            $postData = json_decode($postData, true);
            if (isset($postData['row']['id']) && $postData['row']['id']) {
                $tuple = Be::newTuple('etl_extract_snapshot');
                $tuple->loadBy('extract_log_id', $postData['row']['id']);

                Be::getPlugin('Detail')->setting([
                    'form' => [
                        'items' => [
                            [
                                'name' => 'id',
                                'label' => 'ID',
                            ],
                            [
                                'name' => 'extract_data',
                                'label' => '抽取任务数据',
                                'value' => function ($row) {
                                    return json_encode(json_decode($row['extract_data']), JSON_PRETTY_PRINT);
                                },
                                'driver' => DetailItemCode::class,
                                'language' => 'json',
                            ],
                            [
                                'name' => 'src_ds_data',
                                'label' => '来源数据源数据',
                                'value' => function ($row) {
                                    return json_encode(json_decode($row['src_ds_data']), JSON_PRETTY_PRINT);
                                },
                                'driver' => DetailItemCode::class,
                                'language' => 'json',
                            ],
                            [
                                'name' => 'dst_ds_data',
                                'label' => '目标数据源数据',
                                'value' => function ($row) {
                                    return json_encode(json_decode($row['dst_ds_data']), JSON_PRETTY_PRINT);
                                },
                                'driver' => DetailItemCode::class,
                                'language' => 'json',
                            ],
                            [
                                'name' => 'create_time',
                                'label' => '创建时间',
                            ],
                        ]
                    ]])->setValue($tuple)->execute();
            }
        }
    }

}
