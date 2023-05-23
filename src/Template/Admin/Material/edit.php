<be-head>
    <style>
        .be-page-content .nodes {

        }

        .be-page-content .node,
        .be-page-content .node-add {
            text-align: center;
        }

        .be-page-content .node-on {
            position: relative;
        }

        .be-page-content .node-on:after {
            display: inline-block;
            content: '';
            position: absolute;
            width: 4rem;
            height: 0;
            left: 100%;
            top:50%;
            border-top: #ccc solid 2px;
        }

        .be-page-content .node-line,
        .be-page-content .node-line-arrow {
            width: 2px;
            height: 30px;
            background-color: var(--major-color);
            margin: 0 auto;
            position: relative;
        }

        .be-page-content .node-line-arrow:before,
        .be-page-content .node-line-arrow:after {
            display: inline-block;
            content: '';
            position: absolute;
            width: 10px;
            height: 2px;
            background-color: var(--major-color);
            bottom: 3px;
        }

        .be-page-content .node-line-arrow:before {
            left: -8px;
            transform: rotate3d(0, 0, 1, 45deg);
        }
        .be-page-content .node-line-arrow:after {
            left: 0;
            transform: rotate3d(0, 0, 1, -45deg)
        }

        .be-page-content .field-item-header {
            color: #666;
            background-color: #EBEEF5;
            height: 3rem;
            line-height: 3rem;
            margin-bottom: .5rem;
        }

        .be-page-content  .field-item {
            background-color: #fff;
            border-bottom: #EBEEF5 1px solid;
            padding-top: .5rem;
            padding-bottom: .5rem;
            margin-bottom: 2px;
        }

        .be-page-content  .field-item-op {
            width: 40px;
            line-height: 2.5rem;
            text-align: center;
        }

        .be-page-content  .input-json pre {
            white-space: pre-wrap;
        }

    </style>
</be-head>



<be-page-content>
    <?php
    $formData = [];
    $uiItems = new \Be\AdminPlugin\UiItem\UiItems();
    $rootUrl = \Be\Be::getRequest()->getRootUrl();
    ?>
    <div id="app" v-cloak>

        <el-form ref="formRef" :model="formData" class="be-mb-400">
            <?php
            $formData['id'] = ($this->material ? $this->material->id : '');
            ?>

            <div class="be-row">
                <div class="be-col-auto be-lh-250">
                    <span class="be-c-red">*</span> 素村英文名称：
                </div>
                <div class="be-col-auto">
                    <div class="be-pl-50"></div>
                </div>
                <div class="be-col">
                    <el-form-item style="margin: 0;" prop="name" :rules="[{required: true, message: '请输入素村英文名称', trigger: 'change' }]">
                        <el-input
                                type="text"
                                placeholder="请输入素村英文名称"
                                v-model = "formData.name"
                                size="medium"
                                maxlength="180"
                                show-word-limit>
                        </el-input>
                    </el-form-item>
                    <?php $formData['name'] = $this->material ? $this->material->name : ''; ?>
                </div>
                <div class="be-col-auto">
                    <div class="be-pl-100"></div>
                </div>
                <div class="be-col-auto be-lh-250">
                    <span class="be-c-red">*</span> 素村中文名称：
                </div>
                <div class="be-col-auto">
                    <div class="be-pl-50"></div>
                </div>
                <div class="be-col">
                    <el-form-item style="margin: 0;" prop="label" :rules="[{required: true, message: '请输入素村中文名称', trigger: 'change' }]">
                        <el-input
                                type="text"
                                placeholder="请输入素村中文名称"
                                v-model = "formData.label"
                                size="medium"
                                maxlength="300"
                                show-word-limit>
                        </el-input>
                    </el-form-item>
                    <?php $formData['label'] = $this->material ? $this->material->label : ''; ?>
                </div>
            </div>

            <div class="be-fs-110 be-mt-200">字段配置</div>

            <div class="be-mt-100">

                <div class="be-b-ddd be-mt-50 be-mb-200" v-for="field, fieldIndex in formData.fields" :key="fieldIndex">
                    <div class="be-row">
                        <div class="be-col-auto" style="background-color: var(--major-color-9);">
                            <div class="be-px-100 be-py-150">
                                <div class="be-ta-center be-fs-110">字段 {{fieldIndex + 1}}</div>
                                <div class="be-mt-200">
                                    <el-button class="be-ml-100" size="mini" icon="el-icon-delete" type="danger" @click="fieldDelete(field)" :disabled="formData.fields.length < 2">删除</el-button>
                                </div>
                            </div>
                        </div>
                        <div class="be-col">
                            <div class="be-p-100">
                                <div class="be-row">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 字段英文名称：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50"></div>
                                    </div>
                                    <div class="be-col">
                                        <el-form-item :prop="'fields['+fieldIndex+'].name'" :rules="[{required: true, message: '请输入字段英文名称', trigger: 'change' }]">
                                            <el-input
                                                    type="text"
                                                    placeholder="请输入字段英文名称"
                                                    v-model = "field.name"
                                                    size="medium"
                                                    maxlength="180"
                                                    show-word-limit>
                                            </el-input>
                                        </el-form-item>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 字段中文名称：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50"></div>
                                    </div>
                                    <div class="be-col">
                                        <el-form-item :prop="'fields['+fieldIndex+'].label'" :rules="[{required: true, message: '请输入字段中文名称', trigger: 'change' }]">
                                            <el-input
                                                    type="text"
                                                    placeholder="请输入字段中文名称"
                                                    v-model = "field.label"
                                                    size="medium"
                                                    maxlength="180"
                                                    show-word-limit>
                                            </el-input>
                                        </el-form-item>
                                    </div>
                                </div>

                                <div class="be-row">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 字段类型：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50"></div>
                                    </div>
                                    <div class="be-col">
                                        <el-form-item :prop="'fields['+fieldIndex+'].type'" :rules="[{required: true, message: '请选择字段类型', trigger: 'change' }]">
                                            <el-select v-model="field.type" size="medium">
                                                <el-option label="文本" value="text"></el-option>
                                                <el-option label="HTML" value="html"></el-option>
                                                <el-option label="整数" value="int"></el-option>
                                                <el-option label="浮点数" value="float"></el-option>
                                                <el-option label="布尔（0或1）" value="bool"></el-option>
                                                <el-option label="日期" value="date"></el-option>
                                                <el-option label="日期时间" value="datetime"></el-option>
                                            </el-select>
                                        </el-form-item>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col-auto be-lh-250">
                                        字段默认值：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50"></div>
                                    </div>
                                    <div class="be-col">
                                        <el-input
                                                type="text"
                                                placeholder="请输入字段默认值"
                                                v-model = "field.default"
                                                size="medium">
                                        </el-input>
                                    </div>
                                </div>

                                <div class="be-row">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 字段长度：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50"></div>
                                    </div>
                                    <div class="be-col-auto be-lh-250">
                                        <el-radio v-model="field.lengthType" label="unlimited" @click="field.length = 0;">不限</el-radio>
                                        <el-radio v-model="field.lengthType" label="custom">自定义：</el-radio>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50"></div>
                                    </div>
                                    <div class="be-col">
                                        <el-input-number
                                                :precision="0"
                                                :step="1"
                                                :max="65535"
                                                :controls="false"
                                                :disabled="field.lengthType === 'unlimited'"
                                                v-model.number = "field.length"
                                                size="medium">
                                        </el-input-number>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col-auto be-lh-250">
                                        是否必填：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50"></div>
                                    </div>
                                    <div class="be-col be-lh-250">
                                        <el-switch v-model.number="field.required" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                    </div> <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col-auto be-lh-250">
                                        是否唯一：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50"></div>
                                    </div>
                                    <div class="be-col be-lh-250">
                                        <el-switch v-model.number="field.unique" :active-value="1" :inactive-value="0" size="medium" @change="fieldUpdateUnique(field)"></el-switch>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="be-mt-100">
                <el-button type="warning" size="mini" icon="el-icon-plus" @click="fieldAdd">新增字段</el-button>
            </div>
            <?php
            if ($this->material) {
                $fields = $this->material->fields;
                foreach ($fields as $field) {
                    if ($field->length === 0) {
                        $field->lengthType = 'unlimited';
                    } else {
                        $field->lengthType = 'custom';
                    }
                }
            } else {
                $fields = [
                    (object)[
                        'name' => 'title',
                        'label' => '标题',
                        'type' => 'text',
                        'default' => '',
                        'lengthType' => 'custom',
                        'length' => 300,
                        'required' => 0,
                        'unique' => 0,
                    ],
                    (object)[
                        'name' => 'description',
                        'label' => '描述',
                        'type' => 'html',
                        'default' => '',
                        'lengthType' => 'unlimited',
                        'length' => 0,
                        'required' => 0,
                        'unique' => 0,
                    ],
                ];
            }
            $formData['fields'] = $fields;
            ?>

            <div class="be-mt-200 be-pt-100 be-bt-eee">
                <el-button type="primary" size="medium" :disabled="loading" @click="save('');">保存并关闭</el-button>
                <el-button type="success" size="medium" :disabled="loading" @click="save('stay');">仅保存</el-button>
                <el-button size="medium" :disabled="loading" @click="cancel();">取消</el-button>
            </div>

        </el-form>
    </div>
    <?php
    echo $uiItems->getJs();
    echo $uiItems->getCss();
    ?>
    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>,
                loading: false,
                t: false
                <?php
                echo $uiItems->getVueData();
                ?>
            },
            methods: {

                save: function (command) {

                    let _this = this;
                    this.$refs["formRef"].validate(function (valid) {
                        if (valid) {
                            _this.loading = true;
                            _this.$http.post("<?php echo beAdminUrl('Etl.Material.edit'); ?>", {
                                formData: _this.formData
                            }).then(function (response) {
                                _this.loading = false;
                                //console.log(response);
                                if (response.status === 200) {
                                    var responseData = response.data;
                                    if (responseData.success) {
                                        _this.$message.success(responseData.message);

                                        if (command === 'stay') {
                                            _this.formData.id = responseData.material.id;
                                        } else {
                                            parent.closeAndReload();
                                        }

                                    } else {
                                        if (responseData.message) {
                                            _this.$message.error(responseData.message);
                                        } else {
                                            _this.$message.error("服务器返回数据异常！");
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
                cancel: function () {
                    parent.close();
                },

                fieldAdd: function () {
                    this.formData.fields.push({
                        name: "",
                        label: "",
                        type: "text",
                        default: "",
                        lengthType: "unlimited",
                        length: 0,
                        required: 0,
                        unique: 0,
                    });
                    this.$forceUpdate();
                },
                fieldDelete: function (field) {
                    this.formData.fields.splice(this.formData.fields.indexOf(field), 1);
                    this.$forceUpdate();
                },
                fieldUpdateUnique: function (field) {
                    if (field.unique === 1) {
                        field.required = 1;
                        for(let f of this.formData.fields) {
                            if (f !== field && f.unique === 1) {
                                f.unique = 0;
                            }
                        }
                        this.$forceUpdate();
                    }
                },
                forceUpdate() {
                    this.$forceUpdate();
                }
                <?php
                echo $uiItems->getVueMethods();
                ?>
            }
            <?php
            echo $uiItems->getVueHooks();
            ?>
        });
    </script>
</be-page-content>