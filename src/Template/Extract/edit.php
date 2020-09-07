<be-body>

    <div id="app">
        <el-steps :active="formData.step" finish-status="success" simple>
            <el-step title="配置数据源" icon="el-icon-fa fa-database"></el-step>
            <el-step title="字段映射" icon="el-icon-fa fa-random"></el-step>
            <el-step title="任务参数" icon="el-icon-setting"></el-step>
        </el-steps>

        <div style="height: 20px;"></div>

        <el-form ref="formRef-0" :model="formData" label-width="120px" size="mini" v-if="formData.step == '0'">

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
                    <el-card header="来源" shadow="hover">

                        <el-form-item label="来源数据源" prop="src_ds_id"
                                      :rules="[{required: true, message: '请选择来源数据源', trigger: 'change' }]">
                            <el-select v-model="formData.src_ds_id" placeholder="请选择来源数据源" @change="srcDsChange">
                                <el-option
                                        v-for="(name, id) in dsKeyValues"
                                        :key="id"
                                        :label="name"
                                        :value="id">
                                </el-option>
                            </el-select>
                        </el-form-item>

                        <el-form-item label="来源数据表" prop="src_table"
                                      :rules="[{required: true, message: '请选来源数据表', trigger: 'change' }]">
                            <el-select v-model="formData.src_table" placeholder="请选来源数据表" v-loading="srcTablesLoading">
                                <el-option
                                        v-for="table in srcTables"
                                        :key="table"
                                        :label="table"
                                        :value="table">
                                </el-option>
                            </el-select>
                        </el-form-item>

                    </el-card>
                </el-col>
                <el-col :span="12">
                    <el-card header="目标" shadow="hover">
                        <el-form-item label="目标数据源" prop="dst_ds_id"
                                      :rules="[{required: true, message: '请选择目标数据源', trigger: 'change' }]">
                            <el-select v-model="formData.dst_ds_id" placeholder="请选择目标数据源" @change="dstDsChange">
                                <el-option
                                        v-for="(name, id) in dsKeyValues"
                                        :key="id"
                                        :label="name"
                                        :value="id">
                                </el-option>
                            </el-select>
                        </el-form-item>

                        <el-form-item label="目标数据表" prop="dst_table"
                                      :rules="[{required: true, message: '请选择目标数据表', trigger: 'change' }]">
                            <el-select v-model="formData.dst_table" placeholder="请选择目标数据表" v-loading="dstTablesLoading">
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


        <el-form ref="formRef-1" :model="formData" label-width="120px" size="mini" v-if="formData.step == '1'">
            <el-row :gutter="24">
                <el-col :span="4" style="text-align: center;">
                    <div v-for="item in srcTableFields" style="padding: 2px;">
                        <el-tag type="info">{{item.name}}</el-tag>
                    </div>
                </el-col>

                <el-col :span="16">

                    <el-form-item label="字段映射类型" prop="field_mapping_type"
                                  :rules="[{required: true, message: '字段映射类型', trigger: 'change' }]">
                        <el-select v-model="formData.field_mapping_type" placeholder="字段映射类型">
                            <el-option
                                    v-for="(v, k) in fieldMappingTypeKeyValues"
                                    :key="k"
                                    :label="v"
                                    :value="k">
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-input
                            type="textarea"
                            :autosize="{ minRows: 6, maxRows: 20}"
                            :placeholder="formData.field_mapping_type == '1' ? '字段映射' : '代码处理'"
                            v-model="formData.field_mapping"
                            v-if="formData.field_mapping_type != '0'">
                    </el-input>

                </el-col>

                <el-col :span="4" style="text-align: center;">
                    <div v-for="item in dstTableFields" style="padding: 2px;">
                        <el-tag type="info">{{item.name}}</el-tag>
                    </div>
                </el-col>
            </el-row>

            <el-form-item
                    style="text-align: right; border-top: #eee 1px solid; margin-top: 20px; padding-top: 20px; padding-right: 40px;">
                <el-button type="primary" @click="save" :disabled="loading">保存，进入下一步</el-button>
            </el-form-item>
        </el-form>


        <el-form ref="formRef-2" :model="formData" label-width="120px" size="mini" v-if="formData.step == '2'">
            <el-form-item label="断点类型" prop="breakpoint_type" :rules="[{required: true, message: '请选择断点类型', trigger: 'change' }]">
                <el-select v-model="formData.breakpoint_type" placeholder="请选择断点类型">
                    <el-option
                            v-for="(v, k) in breakpointTypeKeyValues"
                            :key="k"
                            :label="v"
                            :value="k">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="断点字段" prop="breakpoint_field" :rules="[{required: formData.breakpoint_type=='1', message: '请选择断点字段', trigger: 'change' }]" v-if="formData.breakpoint_type=='1'">
                <el-select v-model="formData.breakpoint_field" placeholder="请选择断点字段">
                    <el-option v-for="item in srcTableFields"
                       :key="item.name"
                       :label="item.name"
                       :value="item.name">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="断点" prop="breakpoint" :rules="[{required: formData.breakpoint_type=='1', message: '请选择断点日期时间', trigger: 'change' }]" v-if="formData.breakpoint_type=='1'">
                <el-date-picker
                        v-model="formData.breakpoint"
                        type="datetime"
                        placeholder="请选择断点日期时间"
                        value-format ="yyyy-MM-dd HH:mm:ss">
                </el-date-picker>
            </el-form-item>

            <el-form-item label="断点递增量" prop="breakpoint_step" :rules="[{required: formData.breakpoint_type=='1', message: '请选择断点递增量', trigger: 'change' }]" v-if="formData.breakpoint_type=='1'">
                <el-select v-model="formData.breakpoint_step" placeholder="请选择断点递增量">
                    <el-option
                            v-for="(v, k) in breakpointStepKeyValues"
                            :key="k"
                            :label="v"
                            :value="k">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="执行计划" prop="schedule">
                <el-input v-model="formData.schedule" placeholder="请输入执行计划"></el-input>
            </el-form-item>

            <el-form-item
                    style="text-align: right; border-top: #eee 1px solid; margin-top: 20px; padding-top: 20px; padding-right: 40px;">
                <el-button type="primary" @click="save" :disabled="loading">保存</el-button>
            </el-form-item>

        </el-form>

    </div>

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
                srcTablesLoading: false,
                dstTablesLoading: false,
                srcTables: [],
                dstTables: [],
                dsTables: {},

                fieldMappingTypeKeyValues: <?php echo json_encode($this->fieldMappingTypeKeyValues); ?>,
                srcTableFields: <?php echo json_encode($this->srcTableFields); ?>,
                dstTableFields: <?php echo json_encode($this->dstTableFields); ?>,

                breakpointTypeKeyValues: <?php echo json_encode($this->breakpointTypeKeyValues); ?>,
                breakpointStepKeyValues: <?php echo json_encode($this->breakpointStepKeyValues); ?>,

                loading: false
            },
            methods: {
                srcDsChange: function () {
                    if (this.dsTables[this.formData.src_ds_id] !== undefined) {
                        this.srcTables = this.dsTables[this.formData.src_ds_id];
                    } else {
                        this.srcTablesLoading = true;
                        var _this = this;
                        _this.$http.post("<?php echo beUrl('Etl.Ds.getTableNames'); ?>", {
                            dsId: _this.formData.src_ds_id
                        }).then(function (response) {
                            _this.srcTablesLoading = false;
                            if (response.status == 200) {
                                var responseData = response.data;
                                if (responseData.success) {
                                    _this.dsTables[_this.formData.src_ds_id] = responseData.data.tables;
                                    _this.srcTables = _this.dsTables[_this.formData.src_ds_id];
                                } else {
                                    _this.formData.src_ds_id = "";
                                    if (responseData.message) {
                                        _this.$message.error(responseData.message);
                                    }
                                }
                            }
                        }).catch(function (error) {
                            _this.formData.src_ds_id = "";
                            _this.srcTablesLoading = false;
                            _this.$message.error(error);
                        });
                    }
                },
                dstDsChange: function () {
                    if (this.dsTables[this.formData.dst_ds_id] !== undefined) {
                        this.dstTables = this.dsTables[this.formData.dst_ds_id];
                    } else {
                        this.dstTablesLoading = true;
                        var _this = this;
                        _this.$http.post("<?php echo beUrl('Etl.Ds.getTableNames'); ?>", {
                            dsId: _this.formData.dst_ds_id
                        }).then(function (response) {
                            _this.dstTablesLoading = false;
                            if (response.status == 200) {
                                var responseData = response.data;
                                if (responseData.success) {
                                    _this.dsTables[_this.formData.dst_ds_id] = responseData.data.tables;
                                    _this.dstTables = _this.dsTables[_this.formData.dst_ds_id];
                                } else {
                                    _this.formData.dst_ds_id = "";
                                    if (responseData.message) {
                                        _this.$message.error(responseData.message);
                                    }
                                }
                            }
                        }).catch(function (error) {
                            _this.formData.dst_ds_id = "";
                            _this.dstTablesLoading = false;
                            _this.$message.error(error);
                        });
                    }
                },
                save: function () {
                    var _this = this;
                    this.$refs["formRef-" + _this.formData.step].validate(function (valid) {
                        if (valid) {
                            if (_this.formData.step == 0 &&
                                _this.formData.src_ds_id == _this.formData.dst_ds_id &&
                                _this.formData.src_table == _this.formData.dst_table) {
                                _this.$message.error("来源和目标不能完全一致");
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
                close: function () {
                    parent.close();
                }
            }
        });
    </script>

</be-body>