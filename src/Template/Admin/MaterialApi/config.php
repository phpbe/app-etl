<be-page-content>
    <div id="app" v-cloak>
        <div class="be-p-150 be-bc-fff">
            <div class="be-row be-lh-250 be-bb be-pb-50">
                <div class="be-col-auto">接口开关：</div>
                <div class="be-col-auto be-px-100">
                    <el-switch v-model.number="formData.enable" :active-value="1" :inactive-value="0" size="medium" @change="toggleEnable"></el-switch>
                </div>
            </div>

            <div class="be-row be-lh-250 be-mt-50 be-bb be-pb-50">
                <div class="be-col-auto">接口密钥：</div>
                <div class="be-col-auto be-px-100">
                    <?php echo $this->config->token; ?>
                </div>
                <div class="be-col-auto">
                    <el-link type="primary" icon="el-icon-refresh" :underline="false" href="<?php echo beAdminUrl('Etl.MaterialApi.resetToken'); ?>">重新生成</el-link>
                </div>
            </div>

            <div class="be-row be-lh-250 be-mt-50 be-bb be-pb-50">
                <div class="be-col-auto">素材：</div>
                <div class="be-col-auto be-px-100">
                    <el-select @change="selectMaterial" v-model="materialId" filterable size="medium">
                    <?php
                    foreach ($this->materialIdLabelKeyValues as $id => $label) {
                        ?>
                        <el-option value="<?php echo $id; ?>" label="<?php echo $label; ?>"></el-option>
                        <?php
                    }
                    ?>
                    </el-select>
                </div>
            </div>
        </div>


        <div class="be-p-150 be-bc-fff be-mt-150" v-if="materialId !== ''">
            <el-tabs v-model="activePane">
                <el-tab-pane label="新增" name="create">

                    <div class="be-c-font-4">向素中中增加内容</div>

                    <div class="be-row be-lh-250 be-mt-100">
                        <div class="be-col-auto">接口网址：</div>
                        <div class="be-col-auto be-px-100">
                            <el-tag>
                                <?php echo beUrl('Etl.MaterialApi.create', ['token' => $this->config->token, 'material_id' => $this->material->id]); ?>
                            </el-tag>
                        </div>

                        <div class="be-col-auto">
                            <el-link type="primary" icon="el-icon-document-copy" :underline="false" @click="copyUrl('create')">复制</el-link>
                        </div>
                    </div>

                    <div class="be-lh-250 be-mt-100">POST请求：</div>
                    <div class="be-mt-50">
                        <el-table
                                :data="requestDataCreate"
                                border
                                style="width: 100%">
                            <el-table-column
                                    prop="name"
                                    label="字段名"
                                    width="180">
                            </el-table-column>
                            <el-table-column
                                    prop="required"
                                    label="是否必传"
                                    align="center"
                                    width="180">
                                <template slot-scope="scope">
                                    <el-link v-if="scope.row.required === 1" type="success" icon="el-icon-success" style="cursor:auto;font-size:24px;"></el-link>
                                    <el-link v-else type="info" icon="el-icon-error" style="cursor:auto;font-size:24px;color:#bbb;"></el-link>
                                </template>
                            </el-table-column>
                            <el-table-column
                                    prop="description"
                                    label="说明">
                            </el-table-column>
                        </el-table>
                    </div>

                    <div class="be-lh-250 be-mt-100">响应结果：</div>
                    <div class="be-mt-50">
                        <el-table
                                :data="responseDataEdit"
                                border
                                style="width: 100%">
                            <el-table-column
                                    prop="name"
                                    label="字段名"
                                    width="180">
                            </el-table-column>
                            <el-table-column
                                    prop="required"
                                    label="是否必传"
                                    align="center"
                                    width="180">
                                <template slot-scope="scope">
                                    <el-link v-if="scope.row.required === 1" type="success" icon="el-icon-success" style="cursor:auto;font-size:24px;"></el-link>
                                    <el-link v-else type="info" icon="el-icon-error" style="cursor:auto;font-size:24px;color:#bbb;"></el-link>
                                </template>
                            </el-table-column>
                            <el-table-column
                                    prop="description"
                                    label="说明">
                            </el-table-column>
                        </el-table>
                    </div>

                </el-tab-pane>
                <el-tab-pane label="新增/更新" name="edit">

                    <div class="be-c-font-4">唯一键存在时，更新内容，不存在时，新增内容</div>

                    <div class="be-row be-lh-250 be-mt-100">
                        <div class="be-col-auto">接口网址：</div>
                        <div class="be-col-auto be-px-100">
                            <el-tag>
                                <?php echo beUrl('Etl.MaterialApi.edit', ['token' => $this->config->token, 'material_id' => $this->material->id]); ?>
                            </el-tag>
                        </div>

                        <div class="be-col-auto">
                            <el-link type="primary" icon="el-icon-document-copy" :underline="false" @click="copyUrl('edit')">复制</el-link>
                        </div>
                    </div>

                    <div class="be-lh-250 be-mt-100">POST请求：</div>
                    <div class="be-mt-50">
                        <el-table
                                :data="requestDataEdit"
                                border
                                style="width: 100%">
                            <el-table-column
                                    prop="name"
                                    label="字段名"
                                    width="180">
                            </el-table-column>
                            <el-table-column
                                    prop="required"
                                    label="是否必传"
                                    align="center"
                                    width="180">
                                <template slot-scope="scope">
                                    <el-link v-if="scope.row.required === 1" type="success" icon="el-icon-success" style="cursor:auto;font-size:24px;"></el-link>
                                    <el-link v-else type="info" icon="el-icon-error" style="cursor:auto;font-size:24px;color:#bbb;"></el-link>
                                </template>
                            </el-table-column>
                            <el-table-column
                                    prop="description"
                                    label="说明">
                            </el-table-column>
                        </el-table>
                    </div>

                    <div class="be-lh-250 be-mt-100">响应结果：</div>
                    <div class="be-mt-50">
                        <el-table
                                :data="responseDataEdit"
                                border
                                style="width: 100%">
                            <el-table-column
                                    prop="name"
                                    label="字段名"
                                    width="180">
                            </el-table-column>
                            <el-table-column
                                    prop="required"
                                    label="是否必传"
                                    align="center"
                                    width="180">
                                <template slot-scope="scope">
                                    <el-link v-if="scope.row.required === 1" type="success" icon="el-icon-success" style="cursor:auto;font-size:24px;"></el-link>
                                    <el-link v-else type="info" icon="el-icon-error" style="cursor:auto;font-size:24px;color:#bbb;"></el-link>
                                </template>
                            </el-table-column>
                            <el-table-column
                                    prop="description"
                                    label="说明">
                            </el-table-column>
                        </el-table>
                    </div>


                </el-tab-pane>
                <el-tab-pane label="读取" name="fetch">

                    <div class="be-c-font-4">从素材出读取内容。</div>

                    <div class="be-row be-lh-250 be-mt-100">
                        <div class="be-col-auto">接口网址：</div>
                        <div class="be-col-auto be-px-100">
                            <el-tag>
                                <?php echo beUrl('Etl.MaterialApi.fetch', ['token' => $this->config->token, 'material_id' => $this->material->id]); ?>
                            </el-tag>
                        </div>

                        <div class="be-col-auto">
                            <el-link type="primary" icon="el-icon-document-copy" :underline="false" @click="copyUrl('fetch')">复制</el-link>
                        </div>
                    </div>

                    <div class="be-lh-250 be-mt-100">GET/POST请求：</div>
                    <div class="be-mt-50">
                        <el-table
                                :data="requestDataFetch"
                                border
                                style="width: 100%">
                            <el-table-column
                                    prop="name"
                                    label="字段名"
                                    width="180">
                            </el-table-column>
                            <el-table-column
                                    prop="required"
                                    label="是否必传"
                                    align="center"
                                    width="180">
                                <template slot-scope="scope">
                                    <el-link v-if="scope.row.required === 1" type="success" icon="el-icon-success" style="cursor:auto;font-size:24px;"></el-link>
                                    <el-link v-else type="info" icon="el-icon-error" style="cursor:auto;font-size:24px;color:#bbb;"></el-link>
                                </template>
                            </el-table-column>
                            <el-table-column
                                    prop="description"
                                    label="说明">
                            </el-table-column>
                        </el-table>
                    </div>


                    <div class="be-lh-250 be-mt-100">响应结果：</div>
                    <div class="be-mt-50">
                        <el-table
                                :data="responseDataFetch"
                                border
                                style="width: 100%">
                            <el-table-column
                                    prop="name"
                                    label="字段名"
                                    width="180">
                            </el-table-column>
                            <el-table-column
                                    prop="required"
                                    label="是否必传"
                                    align="center"
                                    width="180">
                                <template slot-scope="scope">
                                    <el-link v-if="scope.row.required === 1" type="success" icon="el-icon-success" style="cursor:auto;font-size:24px;"></el-link>
                                    <el-link v-else type="info" icon="el-icon-error" style="cursor:auto;font-size:24px;color:#bbb;"></el-link>
                                </template>
                            </el-table-column>
                            <el-table-column
                                    prop="description"
                                    label="说明">
                            </el-table-column>
                        </el-table>
                    </div>

                    <div class="be-lh-250 be-mt-100">响应结果中 rows 对象结构：</div>
                    <div class="be-mt-50">
                        <el-table
                                :data="responseDataFetchRows"
                                border
                                style="width: 100%">
                            <el-table-column
                                    prop="name"
                                    label="字段名"
                                    width="180">
                            </el-table-column>
                            <el-table-column
                                    prop="required"
                                    label="是否必传"
                                    align="center"
                                    width="180">
                                <template slot-scope="scope">
                                    <el-link v-if="scope.row.required === 1" type="success" icon="el-icon-success" style="cursor:auto;font-size:24px;"></el-link>
                                    <el-link v-else type="info" icon="el-icon-error" style="cursor:auto;font-size:24px;color:#bbb;"></el-link>
                                </template>
                            </el-table-column>
                            <el-table-column
                                    prop="description"
                                    label="说明">
                            </el-table-column>
                        </el-table>
                    </div>


                </el-tab-pane>
            </el-tabs>
        </div>

    </div>
    <script>
        <?php
        $materialUrls = [];
        foreach ($this->materialIdLabelKeyValues as $id => $label) {
            $materialUrls[$id] = beAdminUrl('Etl.MaterialApi.config', ['material_id' => $id]);
        }

        echo 'let materialUrls = ' . json_encode($materialUrls) . ';';

        $requestDataCreate = [];
        $requestDataEdit = [];
        $requestDataFetch = [
            [
                'name' => 'page',
                'required' => 0,
                'description' => '页数, 大于 0 的整数，默认值: 1',
            ],
            [
                'name' => 'pageSize',
                'required' => 0,
                'description' => '分页大小, 大于 0 小于 5000 的整数，默认值: 100',
            ],
            [
                'name' => 'orderBy',
                'required' => 0,
                'description' => '排序字段, 可选值：create_time - 创建时间/update_time - 更新时间，默认值: create_time',
            ],
            [
                'name' => 'orderByDir',
                'required' => 0,
                'description' => '排序方向, 可选值：asc - 升序/desc - 倒序，默认值: asc',
            ],
        ];



        $responseDataCreate = [
            [
                'name' => 'success',
                'required' => 1,
                'description' => '是否成功, 可选值：true / false',
            ],
            [
                'name' => 'message',
                'required' => 1,
                'description' => '成功或失败信息 ',
            ],
        ];
        $responseDataEdit = [
            [
                'name' => 'success',
                'required' => 1,
                'description' => '是否成功, 可选值：true / false',
            ],
            [
                'name' => 'message',
                'required' => 1,
                'description' => '成功或失败信息 ',
            ],
        ];

        $responseDataFetch = [
            [
                'name' => 'success',
                'required' => 1,
                'description' => '是否成功, 可选值：true / false',
            ],
            [
                'name' => 'message',
                'required' => 1,
                'description' => '成功或失败信息 ',
            ],
            [
                'name' => 'page',
                'required' => 1,
                'description' => '当前页数',
            ],
            [
                'name' => 'pageSize',
                'required' => 1,
                'description' => '分页大小',
            ],
            [
                'name' => 'orderBy',
                'required' => 1,
                'description' => '排序字段',
            ],
            [
                'name' => 'orderByDir',
                'required' => 1,
                'description' => '排序方向',
            ],
            [
                'name' => 'total',
                'required' => 1,
                'description' => '总数据条数',
            ],
            [
                'name' => 'pages',
                'required' => 1,
                'description' => '总页数',
            ],
            [
                'name' => 'rows',
                'required' => 1,
                'description' => '对象数组，符合条件数据列表，对象结构见下表',
            ],
        ];

        $responseDataFetchRows = [
            [
                'name' => 'id',
                'required' => 1,
                'description' => 'UUID',
            ],
        ];

        if ($this->material) {
            foreach ($this->material->fields as $field) {
                $requestField = [
                    'name' => $field->name,
                    'required' => $field->required,
                    'description' => $field->label,
                ];

                if ($field->length > 0) {
                    $requestField['description'] .= ', 最大长度: ' . $field->length . ' 个字符';
                }

                if ($field->default !== '') {
                    $requestField['description'] .= ', 默认值: ' . $field->default;
                }

                $requestDataCreate[] = $requestField;
                $requestDataEdit[] = $requestField;

                $responseDataFetchRows[] = [
                    'name' => $field->name,
                    'required' => 1,
                    'description' => $field->label,
                ];
            }

            $responseDataFetchRows[] = [
                'name' => 'create_time',
                'required' => 1,
                'description' => '创建时间',
            ];

            $responseDataFetchRows[] = [
                'name' => 'update_time',
                'required' => 1,
                'description' => '更新时间',
            ];
        }
        ?>

        let vueCenter = new Vue({
            el: '#app',
            data: {
                formData : {
                    enable: <?php echo $this->config->enable; ?>
                },

                materialId: "<?php echo $this->material ? $this->material->id : ''; ?>",

                activePane: "create",

                requestDataCreate: <?php echo json_encode($requestDataCreate); ?>,
                requestDataEdit: <?php echo json_encode($requestDataEdit); ?>,
                requestDataFetch: <?php echo json_encode($requestDataFetch); ?>,
                responseDataCreate: <?php echo json_encode($responseDataCreate); ?>,
                responseDataEdit: <?php echo json_encode($responseDataEdit); ?>,
                responseDataFetch: <?php echo json_encode($responseDataFetch); ?>,
                responseDataFetchRows: <?php echo json_encode($responseDataFetchRows); ?>,
            },
            methods: {
                toggleEnable() {
                    let _this = this;
                    _this.$http.get("<?php echo beAdminUrl('Etl.MaterialApi.toggleEnable'); ?>", {
                        formData: _this.formData
                    }).then(function (response) {
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.$message.success(responseData.message);
                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                } else {
                                    _this.$message.error("服务器返回数据异常！");
                                }
                            }
                        }
                    }).catch(function (error) {
                        _this.$message.error(error);
                    });
                },
                selectMaterial: function () {
                    window.location.href = materialUrls[this.materialId];
                },
                copyUrl: function (type) {
                    let _this = this;
                    let input = document.createElement('input');

                    let url = "";
                    <?php if ($this->material) { ?>
                    switch (type) {
                        case "create":
                            url = "<?php echo beUrl('Etl.MaterialApi.create', ['token' => $this->config->token, 'material_id' => $this->material->id]); ?>";
                            break;
                        case "edit":
                            url = "<?php echo beUrl('Etl.MaterialApi.edit', ['token' => $this->config->token, 'material_id' => $this->material->id]); ?>";
                            break;
                        case "fetch":
                            url = "<?php echo beUrl('Etl.MaterialApi.fetch', ['token' => $this->config->token, 'material_id' => $this->material->id]); ?>";
                            break;
                    }
                    <?php } ?>

                    input.value = url;
                    document.body.appendChild(input);
                    input.select();
                    try {
                        document.execCommand('Copy');
                        _this.$message.success("接口网址已复制！");
                    } catch {
                    }
                    document.body.removeChild(input);
                }
            }
        });
    </script>
</be-page-content>