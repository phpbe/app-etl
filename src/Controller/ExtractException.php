<?php

namespace Be\App\Etl\Controller;

use Be\Plugin\Form\Item\FormItemDatePickerRange;
use Be\System\Be;
use Be\System\Request;

/**
 * Class ExtractException
 * @package App\Etl\Controller
 *
 * @BePermissionGroup("抽取")
 */
class ExtractException
{
    /**
     * 分类管理
     *
     * @BePermission("异常")
     */
    public function lists()
    {

        $extractId = Request::get('extractId');

        Be::getPlugin('Curd')->setting([

            'label' => '抽取任务异常',
            'table' => 'etl_extract_exception',

            'lists' => [
                'title' => '抽取任务异常',
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
                            'name' => 'message',
                            'label' => '异常信息',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '160',
                        ],
                    ],
                ],

                'operation' => [
                    'label' => '操作',
                    'width' => '90',
                    'items' => [
                        [
                            'label' => '查看',
                            'task' => 'detail',
                            'target' => 'drawer',
                            'drawer' => ['width' => '60%'],
                            'ui' => [
                                'link' => [
                                    'type' => 'success'
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
                            'width' => '60',
                        ],
                        [
                            'name' => 'message',
                            'label' => '异常信息',
                        ],
                        [
                            'name' => 'trace',
                            'label' => '异常跟踪信息	',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '160',
                        ],
                    ]
                ],
            ],

        ])->execute();
    }


}
