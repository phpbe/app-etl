<be-body>

    <div id="app" v-cloak>
        <el-steps :active="formData.step" finish-status="success" simple>
            <el-step title="配置数据源" icon="el-icon-fa fa-database"></el-step>
            <el-step title="字段映射" icon="el-icon-fa fa-random"></el-step>
            <el-step title="任务参数" icon="el-icon-setting"></el-step>
        </el-steps>

        <div style="height: 20px;"></div>

        <el-form ref="formRef-0" :model="formData" label-width="120px" size="mini" v-show="formData.step == '0'">

            <el-form-item label="分类" prop="category_id"
                          :rules="[{required: true, message: '请选择分类', trigger: 'change' }]">
                <el-select v-model="formData.category_id" placeholder="请选择分类">
                    <el-option
                            v-for="(v, k) in categoryKeyValues"
                            :key="k"
                            :label="v"
                            :value="k">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="任务名称" prop="name" :rules="[{required: true, message: '请输入任务名称', trigger: 'blur' }]"
                          style="padding-right: 60px;">
                <el-input v-model="formData.name" placeholder="请输入任务名称"></el-input>
            </el-form-item>

            <el-row :gutter="24" style="margin: 20px;">
                <el-col :span="12">
                    <el-card header="输入" shadow="hover">

                        <el-form-item label="来输入数据源" prop="src_ds_id"
                                      :rules="[{required: true, message: '请选择输入数据源', trigger: 'change' }]">
                            <el-select v-model="formData.src_ds_id" placeholder="请选择输入数据源" @change="srcDsChange">
                                <el-option
                                        v-for="(name, id) in dsKeyValues"
                                        :key="id"
                                        :label="name"
                                        :value="id">
                                </el-option>
                            </el-select>
                        </el-form-item>


                        <el-form-item label="输入类型"
                                      prop="src_type"
                                      :rules="[{required: true, message: '请选择输入类型', trigger: 'change' }]">
                            <el-radio-group v-model="formData.src_type">
                                <el-radio-button v-for="(v, k) in srcTypeKeyValues" :label="k">{{v}}</el-radio-button>
                            </el-radio-group>
                        </el-form-item>

                        <el-form-item label="输入数据表"
                                      prop="src_table"
                                      :rules="[{required: formData.src_type == '0', message: '请选择输入数据表', trigger: 'change' }]"
                                      v-show="formData.src_type == '0'">
                            <el-select v-model="formData.src_table" placeholder="请选输入数据表" v-loading="srcTablesLoading"
                                       filterable>
                                <el-option
                                        v-for="table in srcTables"
                                        :key="table"
                                        :label="table"
                                        :value="table">
                                </el-option>
                            </el-select>
                        </el-form-item>

                        <el-form-item label="输入SQL语句"
                                      prop="src_sql"
                                      :rules="[{required: formData.src_type == '1', message: '请输入SQL语句', trigger: 'blur' }]"
                                      v-show="formData.src_type == '1'">
                            <textarea ref="srcSqlRef" v-model="formData.src_sql"></textarea>
                        </el-form-item>

                    </el-card>
                </el-col>
                <el-col :span="12">
                    <el-card header="输出" shadow="hover">
                        <el-form-item label="输出数据源" prop="dst_ds_id"
                                      :rules="[{required: true, message: '请选择输出数据源', trigger: 'change' }]">
                            <el-select v-model="formData.dst_ds_id" placeholder="请选择输出数据源" @change="dstDsChange">
                                <el-option
                                        v-for="(name, id) in dsKeyValues"
                                        :key="id"
                                        :label="name"
                                        :value="id">
                                </el-option>
                            </el-select>
                        </el-form-item>

                        <el-form-item label="输出数据表" prop="dst_table"
                                      :rules="[{required: true, message: '请选择输出数据表', trigger: 'change' }]">
                            <el-select v-model="formData.dst_table" placeholder="请选择输出数据表" v-loading="dstTablesLoading"
                                       filterable>
                                <el-option
                                        v-for="table in dstTables"
                                        :key="table"
                                        :label="table"
                                        :value="table">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-card>
                </el-col>
            </el-row>

            <el-form-item
                    style="text-align: right; border-top: #eee 1px solid; margin-top: 20px; padding-top: 20px; padding-right: 40px;">
                <el-button type="primary" @click="save" :disabled="loading">保存，进入下一步</el-button>
                <el-button @click="close">取消</el-button>
            </el-form-item>

        </el-form>


        <el-form ref="formRef-1" :model="formData" label-width="120px" size="mini" v-show="formData.step == '1'">

            <el-form-item label="字段映射类型" prop="field_mapping_type"
                          :rules="[{required: true, message: '字段映射类型', trigger: 'change' }]">
                <el-radio-group v-model="formData.field_mapping_type">
                    <el-radio-button v-for="(v, k) in fieldMappingTypeKeyValues" :label="k">{{v}}</el-radio-button>
                </el-radio-group>
            </el-form-item>

            <el-form-item label="字段映射"
                          prop="field_mapping"
                          :rules="[{required: formData.field_mapping_type == '1', message: '请输入字段映射', trigger: 'blur' }]"
                          v-show="formData.field_mapping_type == '1'">

                <div v-loading="dstTableFieldsLoading" v-show="!fieldMappingInput">

                    <div style="padding: 5px;">

                        <el-button @click="fieldMappingSelectAll" size="mini">全选</el-button>
                        <el-button @click="fieldMappingSelectNone" size="mini">全不选</el-button>
                        <el-button @click="fieldMappingSelectMatched" size="mini">选中已匹配的</el-button>
                    </div>

                    <div v-for="dstField in dstTableFields" style="padding: 1px 0;">

                        <el-checkbox v-model="fieldMappingChecked[dstField.name]" @change="forceUpdate"></el-checkbox>

                        <el-select v-model="fieldMapping[dstField.name]" @change="forceUpdate"
                                   :disabled="!fieldMappingChecked[dstField.name]" placeholder="请选择输入表的字段"
                                   v-loading="srcTablesLoading" filterable>
                            <el-option
                                    v-for="srcfield in srcTableFields"
                                    :key="srcfield.name"
                                    :label="srcfield.name"
                                    :value="srcfield.name">
                            </el-option>
                        </el-select>

                        <i class="el-icon-right"></i>

                        <el-tag size="small" :type="fieldMappingChecked[dstField.name] ? 'primary' : 'info'"
                                style="width:200px; text-align:center;">{{dstField.name}}
                        </el-tag>
                    </div>
                </div>

                <div v-show="fieldMappingInput">
                    <el-input
                            type="textarea"
                            :autosize="{minRows: 6, maxRows: 20}"
                            placeholder="字段映射"
                            v-model="formData.field_mapping">
                    </el-input>
                </div>

                <div style="color:#999;">
                    <el-switch
                            v-model="fieldMappingInput"
                            inactive-color="#13ce66"
                            active-text="手工输入字段映射"
                            inactive-text="选择字段映射">
                    </el-switch>
                </div>

                <div style="color:#999;">如果没有权限获取到表结构，可手工输入字段映射。</div>
                <div style="color:#999;" v-show="fieldMappingInput">
                    手工输入时，源字段与目标字段用英文冒号(:)分隔，字段间用英文逗号(,)分隔，例：A1:B1,A2:B2
                </div>

            </el-form-item>

            <el-form-item label="代码处理"
                          prop="field_mapping_code"
                          :rules="[{required: formData.field_mapping_type == '2', message: '请输入代码', trigger: 'blur' }]"
                          v-show="formData.field_mapping_type == '2'">

                <div style="color:#999;">function ($row) {</div>
                <textarea ref="codeRef" v-model="formData.field_mapping_code"></textarea>
                <div style="color:#999;">}</div>
                <div style="color:#999;">$row：将传入输入数据源指定表的一行数据，数组格式，包含所有字段，</div>
                <div style="color:#999;">return 需返回要写入目的的数据，</div>
            </el-form-item>


            <el-form-item
                    style="text-align: right; border-top: #eee 1px solid; margin-top: 20px; padding-top: 20px; padding-right: 40px;">
                <el-button type="primary" @click="gotoStep(0)" :disabled="loading" plain>上一步</el-button>
                <el-button type="primary" @click="save" :disabled="loading">保存，进入下一步</el-button>
            </el-form-item>
        </el-form>


        <el-form ref="formRef-2" :model="formData" label-width="120px" size="mini" v-show="formData.step == '2'">
            <el-form-item label="断点类型" prop="breakpoint_type"
                          :rules="[{required: true, message: '请选择断点类型', trigger: 'change' }]">
                <el-radio-group v-model="formData.breakpoint_type">
                    <el-radio-button v-for="(v, k) in breakpointTypeKeyValues" :label="k">{{v}}</el-radio-button>
                </el-radio-group>
            </el-form-item>

            <el-form-item label="断点字段" prop="breakpoint_field"
                          :rules="[{required: formData.breakpoint_type=='1', message: '请选择断点字段', trigger: 'change' }]"
                          v-if="formData.breakpoint_type=='1'">
                <el-select v-model="formData.breakpoint_field" placeholder="请选择断点字段">
                    <el-option v-for="item in srcTableFields"
                               :key="item.name"
                               :label="item.name"
                               :value="item.name">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="断点" prop="breakpoint"
                          :rules="[{required: formData.breakpoint_type=='1', message: '请选择断点日期时间', trigger: 'change' }]"
                          v-if="formData.breakpoint_type=='1'">
                <el-date-picker
                        v-model="formData.breakpoint"
                        type="datetime"
                        placeholder="请选择断点日期时间"
                        value-format="yyyy-MM-dd HH:mm:ss">
                </el-date-picker>
            </el-form-item>

            <el-form-item label="断点递增量" prop="breakpoint_step"
                          :rules="[{required: formData.breakpoint_type=='1', message: '请选择断点递增量', trigger: 'change' }]"
                          v-if="formData.breakpoint_type=='1'">
                <el-radio-group v-model="formData.breakpoint_step">
                    <el-radio-button v-for="(v, k) in breakpointStepKeyValues" :label="k">{{v}}</el-radio-button>
                </el-radio-group>
            </el-form-item>

            <el-form-item label="断点向前编移量" prop="breakpoint_offset" v-if="formData.breakpoint_type=='1'">
                <el-input-number v-model="formData.breakpoint_offset" :step="1"></el-input-number>
                <div style="color:#999;">
                    此偏移量会将断点范围向前扩充指定的秒数，<br/>
                    例如：断点为: 2020-09-10 00:00:00，断点递增量：一天, 断点向前编移量 86400 秒。<br/>
                    计划任务2010-09-11执行时，断点范围为: 2020-09-09 00:00:00 (2020-09-10向前偏移86400秒) <= T < 2020-09-11
                    00:00:00，即拉取了两天的数据<br/>
                    计划任务2010-09-12执行时，断点范围为: 2020-09-10 00:00:00 (2020-09-11向前偏移86400秒) <= T < 2020-09-12 00:00:00。
                </div>
            </el-form-item>

            <el-form-item label="执行计划" prop="schedule">
                <el-input v-model="formData.schedule" placeholder="请输入执行计划"></el-input>
            </el-form-item>

            <el-form-item
                    style="text-align: right; border-top: #eee 1px solid; margin-top: 20px; padding-top: 20px; padding-right: 40px;">

                <el-button type="primary" @click="gotoStep(1)" :disabled="loading" plain>上一步</el-button>
                <el-button type="primary" @click="save" :disabled="loading">保存</el-button>
            </el-form-item>

        </el-form>

    </div>

    <?php
    $baseUrl = \Be\System\Be::getProperty('Plugin.Form')->getUrl() . '/Template/codemirror-5.57.0/';

    $js = [
        'lib/codemirror.js',
        'mode/clike/clike.js',
        'mode/php/php.js',
        'addon/edit/matchbrackets.js',

        'mode/sql/sql.js',
        'addon/hint/show-hint.js',
        'addon/hint/sql-hint.js',
    ];

    $css = [
        'lib/codemirror.css',
        'addon/hint/show-hint.css',
    ];

    $codeMirrorOptionSrcSql = [
        'theme' => 'default',
        'mode' => 'text/x-sql',
        'lineNumbers' => true,
        'lineWrapping' => true,
        'matchBrackets' => true,
    ];

    $codeMirrorOptionDstSql = [
        'theme' => 'default',
        'mode' => 'text/x-sql',
        'lineNumbers' => true,
        'lineWrapping' => true,
        'matchBrackets' => true,
    ];

    $codeMirrorOptionFieldMappingCode = [
        'theme' => 'default',
        'mode' => 'text/x-php',
        'lineNumbers' => true,
        'lineWrapping' => true,
        'matchBrackets' => true,
    ];

    if (count($js) > 0) {
        $js = array_unique($js);
        foreach ($js as $x) {
            echo '<script src="' . $baseUrl . $x . '"></script>';
        }
    }

    if (count($css) > 0) {
        $css = array_unique($css);
        foreach ($css as $x) {
            echo '<link rel="stylesheet" type="text/css" href="' . $baseUrl . $x . '" />';
        }
    }
    ?>


    <script>
        <?php
        $formData = $this->extract->toArray();

        if ($formData['category_id'] == '0') {
            $formData['category_id'] = '';
        }

        if ($formData['src_ds_id'] == '0') {
            $formData['src_ds_id'] = '';
        }

        if ($formData['dst_ds_id'] == '0') {
            $formData['dst_ds_id'] = '';
        }

        $formData['step'] = 0;
        ?>
        new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>,
                categoryKeyValues: <?php echo json_encode($this->categoryKeyValues); ?>,
                dsKeyValues: <?php echo json_encode($this->dsKeyValues); ?>,
                srcTypeKeyValues: <?php echo json_encode($this->srcTypeKeyValues); ?>,

                srcTablesLoading: false,
                dstTablesLoading: false,
                srcTables: [],
                dstTables: [],
                dsTables: {},

                fieldMappingTypeKeyValues: <?php echo json_encode($this->fieldMappingTypeKeyValues); ?>,

                srcTableFieldsLoading: false,
                dstTableFieldsLoading: false,
                srcTableFields: [],
                dstTableFields: [],
                tableFields: [],

                fieldMappingInput: false,
                fieldMappingChecked: {},
                fieldMapping: {},

                breakpointTypeKeyValues: <?php echo json_encode($this->breakpointTypeKeyValues); ?>,
                breakpointStepKeyValues: <?php echo json_encode($this->breakpointStepKeyValues); ?>,

                loading: false,

                codeMirrorSrcSql: false,
                codeMirrorDstSql: false,
                codeMirrorFieldMappingCode: false
            },
            methods: {
                srcDsChange: function () {
                    if (this.dsTables[this.formData.src_ds_id] !== undefined) {
                        this.srcTables = this.dsTables[this.formData.src_ds_id];
                    } else {
                        this.srcTablesLoading = true;
                        var _this = this;
                        this.loadTables(this.formData.src_ds_id, function () {
                            _this.srcTablesLoading = false;
                            _this.srcTables = _this.dsTables[_this.formData.src_ds_id];
                        }, function () {
                            _this.srcTablesLoading = false;
                            _this.formData.src_ds_id = "";
                        });
                    }
                },
                dstDsChange: function () {
                    if (this.dsTables[this.formData.dst_ds_id] !== undefined) {
                        this.dstTables = this.dsTables[this.formData.dst_ds_id];
                    } else {
                        this.dstTablesLoading = true;
                        var _this = this;
                        this.loadTables(this.formData.dst_ds_id, function () {
                            _this.dstTablesLoading = false;
                            _this.dstTables = _this.dsTables[_this.formData.dst_ds_id];
                        }, function () {
                            _this.dstTablesLoading = false;
                            _this.formData.dst_ds_id = "";
                        });
                    }
                },
                loadTables: function (dsId, fnSuccess, fnFail) {
                    var _this = this;
                    _this.$http.post("<?php echo beUrl('Etl.Ds.getTableNames'); ?>", {
                        dsId: dsId
                    }).then(function (response) {
                        if (response.status == 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.dsTables[dsId] = responseData.data.tables;
                                fnSuccess();
                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                }
                                fnFail();
                            }
                        }
                    }).catch(function (error) {
                        _this.$message.error(error);
                        fnFail();
                    });
                },
                loadSrcTableFields: function () {
                    var srcTable = this.formData.src_type == '0' ? this.formData.src_table : '_sql';

                    if (this.tableFields[this.formData.src_ds_id] !== undefined &&
                        this.tableFields[this.formData.src_ds_id][srcTable] !== undefined) {
                        this.srcTableFields = this.tableFields[this.formData.src_ds_id][srcTable];
                    } else {
                        this.srcTableFieldsLoading = true;

                        var _this = this;
                        var fnSuccess = function () {
                            _this.srcTableFieldsLoading = false;
                            _this.srcTableFields = _this.tableFields[_this.formData.src_ds_id][srcTable];
                            _this.updateFieldMapping();

                            // 生成 CODE
                            if (_this.formData.field_mapping_code == "") {
                                var code = "$return = [];\n";
                                for (var x in _this.fieldMapping) {
                                    if (_this.fieldMapping[x] == "") {
                                        code += "$return['" + x + "'] = '';\n";
                                    } else {
                                        code += "$return['" + x + "'] = $row['" + _this.fieldMapping[x] + "'];\n";
                                    }
                                }
                                code += "return $return;";
                                _this.formData.field_mapping_code = code;
                                _this.codeMirrorFieldMappingCode && _this.codeMirrorFieldMappingCode.setValue(code);
                            }

                        };

                        var fnFail = function () {
                            _this.srcTableFieldsLoading = false;
                        };

                        if (this.formData.src_type == '0') {
                            this.loadTableFields(this.formData.src_ds_id, this.formData.src_table, fnSuccess, fnFail);
                        } else {
                            this.loadSqlFields(this.formData.src_ds_id, this.formData.src_sql, fnSuccess, fnFail);
                        }
                    }
                },
                loadDstTableFields: function () {
                    if (this.tableFields[this.formData.dst_ds_id] !== undefined &&
                        this.tableFields[this.formData.dst_ds_id][this.formData.dst_table] !== undefined) {
                        this.dstTableFields = this.tableFields[this.formData.dst_ds_id][this.formData.dst_table];
                    } else {
                        this.dstTableFieldsLoading = true;
                        var _this = this;
                        this.loadTableFields(this.formData.dst_ds_id, this.formData.dst_table, function () {
                            _this.dstTableFieldsLoading = false;
                            _this.dstTableFields = _this.tableFields[_this.formData.dst_ds_id][_this.formData.dst_table];
                            _this.updateFieldMapping();

                            _this.loadSrcTableFields();
                        }, function () {
                            _this.dstTableFieldsLoading = false;
                        });
                    }
                },
                loadSqlFields: function (dsId, sql, fnSuccess, fnFail) {
                    var _this = this;
                    _this.$http.post("<?php echo beUrl('Etl.Ds.getSqlFields'); ?>", {
                        dsId: dsId,
                        sql: sql
                    }).then(function (response) {
                        if (response.status == 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                if (_this.tableFields[dsId] == undefined) {
                                    _this.tableFields[dsId] = {};
                                }

                                _this.tableFields[dsId]['_sql'] = responseData.data.fields;
                                fnSuccess();
                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                }
                                fnFail();
                            }
                        }
                    }).catch(function (error) {
                        _this.$message.error(error);
                        fnFail();
                    });
                },
                loadTableFields: function (dsId, table, fnSuccess, fnFail) {
                    var _this = this;
                    _this.$http.post("<?php echo beUrl('Etl.Ds.getTableFields'); ?>", {
                        dsId: dsId,
                        table: table
                    }).then(function (response) {
                        if (response.status == 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                if (_this.tableFields[dsId] == undefined) {
                                    _this.tableFields[dsId] = {};
                                }

                                _this.tableFields[dsId][table] = responseData.data.fields;
                                fnSuccess();
                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                }
                                fnFail();
                            }
                        }
                    }).catch(function (error) {
                        _this.$message.error(error);
                        fnFail();
                    });
                },
                updateFieldMapping: function () {
                    var hasSrcTableFields = this.srcTableFields.length > 0;
                    if (this.dstTableFields.length > 0) {
                        for (var i = 0; i < this.dstTableFields.length; i++) {
                            if (hasSrcTableFields) {
                                var srcName = '';
                                for (var j = 0; j < this.srcTableFields.length; j++) {
                                    if (this.dstTableFields[i].name == this.srcTableFields[j].name) {
                                        srcName = this.srcTableFields[j].name;
                                        break;
                                    }
                                }
                                this.fieldMapping[this.dstTableFields[i].name] = srcName;
                                this.fieldMappingChecked[this.dstTableFields[i].name] = true;
                            } else {
                                this.fieldMapping[this.dstTableFields[i].name] = "";
                                this.fieldMappingChecked[this.dstTableFields[i].name] = false;
                            }
                        }
                    }
                },
                gotoStep: function (step) {
                    this.formData.step = step;
                    this.$forceUpdate();
                },
                save: function () {
                    if (this.formData.step == 1 && this.formData.field_mapping_type == '1' && !this.fieldMappingInput) {
                        var isAllMapping = true;
                        var fieldMapping = [];
                        for (var x in this.fieldMapping) {
                            if (this.fieldMappingChecked[x]) {
                                if (this.fieldMapping[x] == "") {
                                    isAllMapping = false;
                                    break;
                                }

                                fieldMapping.push(this.fieldMapping[x] + ":" + x);
                            }
                        }

                        if (isAllMapping) {
                            this.formData.field_mapping = fieldMapping.join(",");
                        } else {
                            this.formData.field_mapping = "";
                        }
                    }

                    var _this = this;
                    this.$refs["formRef-" + _this.formData.step].validate(function (valid) {
                        if (valid) {
                            if (_this.formData.step == 0 &&
                                _this.formData.src_ds_id == _this.formData.dst_ds_id &&
                                _this.formData.src_type == 0 &&
                                _this.formData.src_table == _this.formData.dst_table) {
                                _this.$message.error("输入和输出不能完全一致");
                                return false;
                            }

                            _this.loading = true;
                            _this.$http.post("<?php echo beUrl('Etl.Extract.edit'); ?>", {
                                formData: _this.formData
                            }).then(function (response) {
                                _this.loading = false;
                                if (response.status == 200) {
                                    var responseData = response.data;
                                    if (responseData.success) {
                                        if (_this.formData.step == 0) {
                                            _this.formData.id = responseData.data.extract.id;
                                            _this.formData.step = 1;

                                            _this.loadDstTableFields();

                                        } else if (_this.formData.step == 1) {
                                            _this.formData.step = 2;
                                        } else if (_this.formData.step == 2) {
                                            _this.formData.step = 3;
                                            parent.closeAndReload();
                                        }
                                    } else {
                                        if (responseData.message) {
                                            _this.$message.error(responseData.message);
                                        }
                                    }
                                }
                            }).catch(function (error) {
                                _this.loading = false;
                                _this.$message.error(error);
                            });

                        } else {
                            return false;
                        }
                    });
                },
                fieldMappingSelectAll: function () {
                    for (var x in this.fieldMappingChecked) {
                        this.fieldMappingChecked[x] = true;
                    }
                    this.forceUpdate();
                },
                fieldMappingSelectNone: function () {
                    for (var x in this.fieldMappingChecked) {
                        this.fieldMappingChecked[x] = false;
                    }
                    this.forceUpdate();
                },
                fieldMappingSelectMatched: function () {
                    for (var x in this.fieldMapping) {
                        this.fieldMappingChecked[x] = this.fieldMapping[x] != "";
                    }
                    this.forceUpdate();
                },
                close: function () {
                    parent.close();
                },
                forceUpdate: function () {
                    this.$forceUpdate();
                }
            },
            mounted: function () {

                if (this.formData.src_ds_id) {
                    this.srcDsChange();
                }

                if (this.formData.dst_ds_id) {
                    this.dstDsChange();
                }

                this.codeMirrorSrcSql = CodeMirror.fromTextArea(this.$refs.srcSqlRef, <?php echo json_encode($codeMirrorOptionSrcSql) ?>);
                //this.codeMirrorDstSql = CodeMirror.fromTextArea(this.$refs.codeRef, <?php echo json_encode($codeMirrorOptionDstSql) ?>);
                this.codeMirrorFieldMappingCode = CodeMirror.fromTextArea(this.$refs.codeRef, <?php echo json_encode($codeMirrorOptionFieldMappingCode) ?>);
            },
            updated: function () {
                this.codeMirrorSrcSql && this.codeMirrorSrcSql.refresh();
                //this.codeMirrorDstSql && this.codeMirrorDstSql.refresh();
                this.codeMirrorFieldMappingCode && this.codeMirrorFieldMappingCode.refresh();

                this.formData.src_sql = this.codeMirrorSrcSql.getValue();
                //this.formData.dst_sql = this.codeMirrorDstSql.getValue();
                this.formData.field_mapping_code = this.codeMirrorFieldMappingCode.getValue();
            }
        })
        ;
    </script>

</be-body>