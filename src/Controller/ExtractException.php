<?php

namespace Be\App\Etl\Controller;

use Be\Plugin\Form\Item\FormItemDatePickerRange;
use Be\System\Be;
use Be\System\Controller;

/**
 * Class ExtractException
 * @package App\Etl\Controller
 *
 * @BeMenuGroup("抽取", icon="el-icon-fa fa-copy")
 * @BePermissionGroup("抽取")
 */
class ExtractException extends Controller
{
    /**
     * 分类管理
     *
     * @BeMenu("异常", icon="el-icon-fa fa-warning")
     * @BePermission("异常")
     */
    public function lists()
    {

        $extractKeyValues = Be::getService('Etl.Extract')->getIdNameKeyValues();

        Be::getPlugin('Curd')->setting([

            'label' => '抽取任务异常',
            'table' => 'etl_extract_exception',

            'lists' => [
                'title' => '抽取任务异常',

                'form' => [
                    'items' => [
                        [
                            'name' => 'extract_id',
                            'label' => '抽取任务',
                            'keyValues' => $extractKeyValues,
                        ],
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
                            'name' => 'extract_id',
                            'label' => '抽取任务',
                            'keyValues' => $extractKeyValues,
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
                    'width' => '60',
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
                            'name' => 'extract_id',
                            'label' => '抽取任务',
                            'keyValues' => $extractKeyValues,
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
