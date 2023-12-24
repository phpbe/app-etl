<?php

namespace Be\App\Etl\Controller\Admin;

use Be\AdminPlugin\AdminPluginException;
use Be\AdminPlugin\Detail\Item\DetailItemHtml;
use Be\AdminPlugin\Form\Item\FormItemDatePicker;
use Be\AdminPlugin\Form\Item\FormItemDateTimePicker;
use Be\AdminPlugin\Form\Item\FormItemInput;
use Be\AdminPlugin\Form\Item\FormItemInputNumberFloat;
use Be\AdminPlugin\Form\Item\FormItemInputNumberInt;
use Be\AdminPlugin\Form\Item\FormItemInputTextArea;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Form\Item\FormItemSwitch;
use Be\AdminPlugin\Form\Item\FormItemTinymce;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemLink;
use Be\App\ServiceException;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 存储管理器
 *
 * @BeMenuGroup("素材")
 * @BePermissionGroup("素材")
 */
class MaterialItem extends Auth
{


    /**
     * @BePermission("素材内容", ordering="2.2")
     */
    public function select()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $materialIdNameKeyValues = Be::getService('App.Etl.Admin.Material')->getIdNameKeyValues();
        $response->set('materialIdNameKeyValues', $materialIdNameKeyValues);

        $response->set('title', '选择素材');
        $response->display();
    }


    /**
     * @BeMenu("素材内容", icon = "bi-journals", ordering="2.2")
     * @BePermission("素材内容")
     */
    public function index()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $service = Be::getService('App.Etl.Admin.Material');

        $materialIdNameKeyValues = $service->getIdNameKeyValues();

        $materialId = Be::getRequest()->post('material_id', '');
        if ($materialId === '') {
            $materialId = Be::getRequest()->get('material_id', '');
        }

        if (!isset($materialIdNameKeyValues[$materialId])) {
            $response->redirect(beAdminUrl('Etl.MaterialItem.select'));
            return;
        }

        $material = $service->getMaterial($materialId);

        $tableItems = [
            [
                'driver' => TableItemSelection::class,
                'width' => '50',
            ],
        ];
        $detailItems = [
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                'name' => 'unique_key',
                'label' => '唯一键',
            ],
        ];
        $createItems = [
            [
                'name' => 'material_id',
                'label' => '素材',
                'driver' => FormItemSelect::class,
                'keyValues' => $materialIdNameKeyValues,
                'value' => $materialId,
                'disabled' => true,
            ],
        ];
        $editItems = [
            [
                'name' => 'material_id',
                'label' => '素材',
                'driver' => FormItemSelect::class,
                'keyValues' => $materialIdNameKeyValues,
                'value' => $materialId,
                'disabled' => true,
            ],
        ];

        $importItems = [];

        $exportItems = [
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                'name' => 'unique_key',
                'label' => '唯一键',
            ],
        ];

        foreach ($material->fields as $field) {
            if ($field->type === 'text'){
                if (count($tableItems) === 1) {
                    $tableItems[] = [
                        'name' => $field->name,
                        'label' => $field->label,
                        'value' => function($row) use($field) {
                            $data = unserialize($row['data']);
                            return $data[$field->name] ?? '';
                        },
                        'align' => 'left',
                        'driver' => TableItemLink::class,
                        'task' => 'detail',
                        'target' => 'drawer',
                        'drawer' => [
                            'width' => '80%',
                        ],
                    ];
                }
            }

            $formItem = [
                'name' => $field->name,
                'label' => $field->label,
            ];

            $detailItem = [
                'name' => $field->name,
                'label' => $field->label,
                'value' => function($row) use($field) {
                    $data = unserialize($row['data']);
                    return $data[$field->name] ?? '';
                },
            ];

            switch ($field->type) {
                case 'text':
                    $formItem['driver'] = FormItemInput::class;
                    break;
                case 'textarea':
                    $formItem['driver'] = FormItemInputTextArea::class;
                    break;
                case 'html':
                    $formItem['driver'] = FormItemTinymce::class;
                    $detailItem['driver'] = DetailItemHtml::class;
                    break;
                case 'int':
                    $formItem['driver'] = FormItemInputNumberInt::class;
                    break;
                case 'float':
                    $formItem['driver'] = FormItemInputNumberFloat::class;
                    break;
                case 'bool':
                    $formItem['driver'] = FormItemSwitch::class;
                    break;
                case 'date':
                    $formItem['driver'] = FormItemDatePicker::class;
                    break;
                case 'datetime':
                    $formItem['driver'] = FormItemDateTimePicker::class;
                    break;
            }

            if ($field->length > 0) {
                $formItem['ui'] = [
                    'maxlength' => $field->length,
                    'show-word-limit' => true,
                ];
            }

            if ($field->required === 1) {
                $formItem['required'] = true;
            }

            $createItems[] = $formItem;

            $formItem['value'] = function($row) use($field) {
                $data = unserialize($row['data']);
                return $data[$field->name] ?? '';
            };

            $editItems[] = $formItem;

            $detailItems[] =  $detailItem;

            $importItems[] = [
                'name' => $field->name,
                'label' => $field->label,
            ];;

            $exportItems[] = [
                'name' => $field->name,
                'label' => $field->label,
                'value' => function($row) use($field) {
                    $data = unserialize($row['data']);
                    return $data[$field->name] ?? '';
                },
            ];;
        }

        $tableItems[] = [
            'name' => 'create_time',
            'label' => '创建时间',
            'width' => '180',
            'sortable' => true,
        ];
        $tableItems[] = [
            'name' => 'update_time',
            'label' => '更新时间',
            'width' => '180',
            'sortable' => true,
        ];

        $detailItems[] = [
            'name' => 'create_time',
            'label' => '创建时间',
        ];
        $detailItems[] = [
            'name' => 'update_time',
            'label' => '更新时间',
        ];


        $exportItems[] = [
            'name' => 'create_time',
            'label' => '创建时间',
        ];
        $exportItems[] = [
            'name' => 'update_time',
            'label' => '更新时间',
        ];

        Be::getAdminPlugin('Curd')->setting([
            'label' => '素材内容',
            'table' => 'etl_material_item',
            'grid' => [
                'title' => '素材内容',

                'filter' => [
                    ['material_id', $materialId],
                ],

                'orderBy' => 'create_time',
                'orderByDir' => 'DESC',

                'form' => [
                    'items' => [
                        [
                            'name' => 'material_id',
                            'label' => '素材',
                            'driver' => FormItemSelect::class,
                            'required' => true,
                            'keyValues' => $materialIdNameKeyValues,
                            'value' => $materialId,
                        ],
                    ],
                ],

                'titleToolbar' => [
                    'items' => [
                        [
                            'label' => '导入',
                            'driver' => ToolbarItemLink::class,
                            'ui' => [
                                'icon' => 'bi-upload',
                            ],
                            'task' => 'import',
                        ],
                        [
                            'label' => '导出',
                            'driver' => ToolbarItemDropDown::class,
                            'ui' => [
                                'icon' => 'bi-download',
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
                            'label' => '新增素材内容',
                            'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ],
                            'task' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'drawer' => [
                                'width' => '80%'
                            ],
                        ],
                    ],
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量删除',
                            'task' => 'delete',
                            'target' => 'ajax',
                            'confirm' => '此操作将从数据库彻底删除，确认要执行么？',
                            'ui' => [
                                'icon' => 'el-icon-delete',
                                'type' => 'danger'
                            ],
                        ],
                    ],
                ],

                'table' => [

                    // 未指定时取表的所有字段
                    'items' => $tableItems,
                    'operation' => [
                        'label' => '操作',
                        'width' => '120',
                        'items' => [
                            [
                                'label' => '',
                                'tooltip' => '编辑',
                                'ui' => [
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-edit',
                                'task' => 'edit',
                                'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                                'drawer' => [
                                    'width' => '80%'
                                ],
                            ],
                            [
                                'label' => '',
                                'tooltip' => '删除',
                                'ui' => [
                                    'type' => 'danger',
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-delete',
                                'confirm' => '此操作将从数据库彻底删除，确认要执行么？',
                                'task' => 'delete',
                                'target' => 'ajax',
                            ],
                        ]
                    ],
                ],
            ],

            'create' => [
                'title' => '新建素材',
                'form' => [
                    'items' => $createItems,
                ],
                'events' => [
                    'before' => function ($tuple, $postData) {
                        $formData = $postData['formData'];
                        Be::getService('App.Etl.Admin.MaterialItem')->processData($tuple, $formData);
                    }
                ],
            ],

            'edit' => [
                'title' => '编辑管理员',
                'form' => [
                    'items' => $editItems,
                ],
                'events' => [
                    'before' => function ($tuple, $postData) {
                        $formData = $postData['formData'];
                        Be::getService('App.Etl.Admin.MaterialItem')->processData($tuple, $formData);
                    }
                ],
            ],

            'detail' => [
                'title' => '文章详情',
                'form' => [
                    'items' => $detailItems
                ],
            ],

            'import' => [
                'mapping' => [
                    'items' => $importItems,
                ],
                'events' => [
                    'before' => function ($tuple, $row) use ($materialId)  {
                        $tuple->material_id = $materialId;

                        $row['material_id'] = $materialId;
                        Be::getService('App.Etl.Admin.MaterialItem')->processData($tuple, $row);
                    }
                ],
            ],

            'export' => [
                'items' => $exportItems,
            ],

        ])->execute();
    }

}
