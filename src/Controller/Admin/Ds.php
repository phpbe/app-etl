<?php

namespace Be\App\Etl\Controller\Admin;


use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\Be;
use Be\Db\Tuple;
use Be\AdminPlugin\Detail\Item\DetailItemSwitch;
use Be\AdminPlugin\Form\Item\FormItemAutoComplete;
use Be\AdminPlugin\Form\Item\FormItemCustom;
use Be\AdminPlugin\Form\Item\FormItemInputNumberInt;
use Be\AdminPlugin\Form\Item\FormItemInputPassword;
use Be\AdminPlugin\Form\Item\FormItemInputTextArea;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Form\Item\FormItemSwitch;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Table\Item\TableItemSwitch;

/**
 * Class Ds
 * @package Be\App\Etl\Controller\Admin
 *
 */
class Ds
{
    /**
     * 数据源管理
     *
     * @BeMenu("数据源", icon="bi-database", ordering="1")
     * @BePermission("数据源")
     */
    public function lists()
    {
        $typeKeyValues = [
            'mysql' => 'Mysql',
            'oracle' => 'Oracel',
            'mssql' => 'SQL Server',
        ];

        $configDb = Be::getConfig('App.System.Db');

        Be::getAdminPlugin('Curd')->setting([

            'label' => '数据源',
            'table' => 'etl_ds',

            'grid' => [
                'title' => '数据源',

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
                            'task' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
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
                    'items' => [
                        [
                            'driver' => TableItemSelection::class,
                            'width' => '50',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'driver' => TableItemLink::class,
                            'align' => 'left',
                            'task' => 'detail',
                            'drawer' => [
                                'width' => '80%'
                            ],
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
                        /*
                        [
                            'name' => 'db_charset',
                            'label' => '字符集',
                        ],
                        */
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
                            'width' => '180',
                        ],
                    ],

                    'operation' => [
                        'label' => '操作',
                        'width' => '120',
                        'items' => [
                            [
                                'label' => '编辑',
                                'task' => 'edit',
                                'target' => 'drawer',
                                'ui' => [
                                    'type' => 'primary'
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
                                    'type' => 'danger'
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
                        /*
                        [
                            'name' => 'db_charset',
                            'label' => '字符集',
                        ],
                        */
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
                            'driver' => FormItemCustom::class,
                            'html' => '<el-form-item><el-button type="primary" @click="loadSystemDb"  size="mini" plain>系统主库</el-button></el-form-item>'
                        ],
                        [
                            'name' => 'type',
                            'label' => '驱动类型',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $typeKeyValues,
                            'required' => true,
                            'ui' => [
                                '@change' => 'switch(formData.type) {case \'mysql\': formData.db_port=\'3306\';break;case \'mssql\': formData.db_port=\'1433\';break;case \'oracle\': formData.db_port=\'1521\';break;}',
                            ]
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
                            'driver' => FormItemCustom::class,
                            'html' => '<el-form-item><el-button type="success" @click="testDb" v-loading="testDbLoading" size="mini" plain>测试连接，并获取库名列表</el-button></el-form-item>'
                        ],
                        [
                            'name' => 'db_name',
                            'label' => '库名',
                            'driver' => FormItemAutoComplete::class,
                            'required' => true,
                        ],
                        /*
                        [
                            'name' => 'db_charset',
                            'label' => '字符集',
                        ],
                        */
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
                'vueData' => [
                    'testDbLoading' => false, // 测试数据库连接中
                ],
                'vueMethods' => [
                    'loadSystemDb' => 'function() {
                        this.formData.type = \'' . $configDb->master['driver'] . '\';
                        this.formData.db_host = \'' . $configDb->master['host'] . '\';
                        this.formData.db_port = \'' . $configDb->master['port'] . '\';
                        this.formData.db_user = \'' . $configDb->master['username'] . '\';
                        this.formData.db_pass = \'' . $configDb->master['password'] . '\';
                        this.formData.db_name = \'' . $configDb->master['name'] . '\';
                    }',
                    'testDb' => 'function() {
                        var _this = this;
                        this.testDbLoading = true;
                        this.$http.post("' . beAdminUrl('Etl.Ds.testDb') . '", {
                                formData: _this.formData
                            }).then(function (response) {
                                _this.testDbLoading = false;
                                //console.log(response);
                                if (response.status == 200) {
                                    var responseData = response.data;
                                    if (responseData.success) {
                                        var message;
                                        if (responseData.message) {
                                            message = responseData.message;
                                        } else {
                                            message = \'连接成功！\';
                                        }
                                        _this.$message.success(message);
                                        var suggestions = [];
                                        for(var x in responseData.data.databases) {
                                            suggestions.push({
                                                "value" : responseData.data.databases[x]
                                            });
                                        }
                                        _this.formItems.db_name.suggestions = suggestions;
                                    } else {
                                        if (responseData.message) {
                                            _this.$message.error(responseData.message);
                                        }
                                    }
                                }
                            }).catch(function (error) {
                                _this.testDbLoading = false;
                                _this.$message.error(error);
                            });
                    }',
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
                            'driver' => FormItemCustom::class,
                            'html' => '<el-form-item><el-button type="primary" @click="loadSystemDb"  size="mini" plain>系统主库</el-button></el-form-item>'
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
                            'driver' => FormItemCustom::class,
                            'html' => '<el-form-item><el-button type="success" @click="testDb" v-loading="testDbLoading" size="mini" plain>测试连接，并获取库名列表</el-button></el-form-item>'
                        ],
                        [
                            'name' => 'db_name',
                            'label' => '库名',
                            'driver' => FormItemAutoComplete::class,
                            'required' => true,
                        ],
                        /*
                        [
                            'name' => 'db_charset',
                            'label' => '字符集',
                        ],
                        */
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
                ],
                'vueData' => [
                    'testDbLoading' => false, // 测试数据库连接中
                ],
                'vueMethods' => [
                    'loadSystemDb' => 'function() {
                        this.formData.type = \'' . $configDb->master['driver'] . '\';
                        this.formData.db_host = \'' . $configDb->master['host'] . '\';
                        this.formData.db_port = \'' . $configDb->master['port'] . '\';
                        this.formData.db_user = \'' . $configDb->master['username'] . '\';
                        this.formData.db_pass = \'' . $configDb->master['password'] . '\';
                        this.formData.db_name = \'' . $configDb->master['name'] . '\';
                    }',
                    'testDb' => 'function() {
                        var _this = this;
                        this.testDbLoading = true;
                        this.$http.post("' . beAdminUrl('Etl.Ds.testDb') . '", {
                                formData: _this.formData
                            }).then(function (response) {
                                _this.testDbLoading = false;
                                //console.log(response);
                                if (response.status == 200) {
                                    var responseData = response.data;
                                    if (responseData.success) {
                                        var message;
                                        if (responseData.message) {
                                            message = responseData.message;
                                        } else {
                                            message = \'连接成功！\';
                                        }
                                        _this.$message.success(message);
                                        var suggestions = [];
                                        for(var x in responseData.data.databases) {
                                            suggestions.push({
                                                "value" : responseData.data.databases[x]
                                            });
                                        }
                                        _this.formItems.db_name.suggestions = suggestions;
                                    } else {
                                        if (responseData.message) {
                                            _this.$message.error(responseData.message);
                                        }
                                    }
                                }
                            }).catch(function (error) {
                                _this.testDbLoading = false;
                                _this.$message.error(error);
                            });
                    }',
                ],
            ],

        ])->execute();
    }

    /**
     * @BePermission("*")
     */
    public function testDb()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $postData = $request->json();
            $databases = Be::getService('App.Etl.Admin.Ds')->testDb($postData['formData']);
            $response->set('success', true);
            $response->set('data', [
                'databases' => $databases,
            ]);
            $response->json();
        } catch (\Exception $e) {
            $response->set('success', false);
            $response->set('message', $e->getMessage());
            $response->set('trace', $e->getTrace());
            $response->json();
        }
    }

    /**
     * @BePermission("*")
     */
    public function getTableNames()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $postData = $request->json();
            $tables = Be::getService('App.Etl.Admin.Ds')->getTableNames($postData['dsId']);
            $response->set('success', true);
            $response->set('data', [
                'tables' => $tables,
            ]);
            $response->json();
        } catch (\Exception $e) {
            $response->set('success', false);
            $response->set('message', $e->getMessage());
            $response->json();
        }
    }

    /**
     * @BePermission("*")
     */
    public function getTableFields()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $postData = $request->json();
            $fields = Be::getService('App.Etl.Admin.Ds')->getTableFields($postData['dsId'], $postData['table']);
            $response->set('success', true);
            $response->set('data', [
                'fields' => $fields,
            ]);
            $response->json();
        } catch (\Exception $e) {
            $response->set('success', false);
            $response->set('message', $e->getMessage());
            $response->json();
        }
    }


    /**
     * @BePermission("*")
     */
    public function getSqlFields()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $postData = $request->json();
            $fields = Be::getService('App.Etl.Admin.Ds')->getSqlFields($postData['dsId'], $postData['sql']);
            $response->set('success', true);
            $response->set('data', [
                'fields' => $fields,
            ]);
            $response->json();
        } catch (\Exception $e) {
            $response->set('success', false);
            $response->set('message', $e->getMessage());
            $response->json();
        }
    }


}
