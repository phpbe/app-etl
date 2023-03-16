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
            width: 3rem;
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


    </style>
</be-head>


<be-north>
    <div id="app-north">
        <div class="be-row">
            <div class="be-col">
                <div style="padding: 1.25rem 0 0 2rem;">
                    <el-link icon="el-icon-back" href="<?php echo $this->backUrl; ?>">返回加工任务列表</el-link>
                </div>
            </div>
            <div class="be-col-auto">
                <div style="padding: .75rem 2rem 0 0;">
                    <el-button size="medium" :disabled="loading" @click="vueCenter.cancel();">取消</el-button>
                    <el-button type="success" size="medium" :disabled="loading" @click="vueCenter.save('stay');">仅保存</el-button>
                    <el-button type="primary" size="medium" :disabled="loading" @click="vueCenter.save('');">保存并返回</el-button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let vueNorth = new Vue({
            el: '#app-north',
            data: {
                loading: false,
            }
        });
    </script>
</be-north>


<be-page-content>
    <?php
    $formData = [];
    $uiItems = new \Be\AdminPlugin\UiItem\UiItems();
    $rootUrl = \Be\Be::getRequest()->getRootUrl();
    ?>
    <div id="app" v-cloak>

        <el-form ref="formRef" :model="formData" class="be-mb-400">
            <?php
            $formData['id'] = ($this->flow ? $this->flow->id : '');
            ?>

            <div class="be-row">
                <div class="be-col-24 be-md-col">
                    <div class="be-p-150 be-bc-fff">
                        <div class="be-row">
                            <div class="be-col-24 be-md-col-auto be-lh-250">
                                <span class="be-c-red">*</span> 名称：
                            </div>
                            <div class="be-col-24 be-md-col-auto">
                                <div class="be-pl-50 be-pt-100"></div>
                            </div>
                            <div class="be-col-24 be-md-col">
                                <el-form-item style="margin: 0;" prop="name" :rules="[{required: true, message: '请输入加工任务名称', trigger: 'change' }]">
                                    <el-input
                                            type="text"
                                            placeholder="请输入加工任务名称"
                                            v-model = "formData.name"
                                            size="medium"
                                            maxlength="200"
                                            show-word-limit>
                                    </el-input>
                                </el-form-item>
                                <?php $formData['name'] = $this->flow->name; ?>
                            </div>
                        </div>

                        <div class="be-row be-mt-100">
                            <div class="be-col-24 be-md-col-auto be-lh-250">
                                素材分类：
                            </div>
                            <div class="be-col-24 be-md-col-auto">
                                <div class="be-pl-50 be-pt-100"></div>
                            </div>
                            <div class="be-col-24 be-md-col">
                                <el-select v-model="formData.category_id" size="medium">
                                    <el-option label="无分类" value=""></el-option>
                                    <?php
                                    foreach ($this->categoryKeyValues as $key => $val) {
                                        ?>
                                        <el-option label="<?php echo $val; ?>" value="<?php echo $key; ?>"></el-option>
                                        <?php
                                    }
                                    ?>
                                </el-select>
                                <?php
                                $formData['category_id'] = $this->flow->category_id;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="be-col-24 be-md-col-auto">
                    <div class="be-pl-150 be-pt-150"></div>
                </div>
                <div class="be-col-24 be-md-col-auto">
                    <div class="be-p-150 be-bc-fff" style="height: 100%;">
                        <div class="be-row">
                            <div class="be-col">是否启用：</div>
                            <div class="be-col-auto">
                                <el-switch v-model.number="formData.is_enable" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                            </div>
                        </div>
                        <?php $formData['is_enable'] = $this->flow->is_enable; ?>
                    </div>
                </div>
            </div>



            <div class="be-mt-150 be-p-150 be-bc-fff">
                <div class="be-fs-110">处理节点</div>

                <div class="be-row be-mt-200">
                    <div class="be-col-auto">
                        <div class="nodes">

                            <div class="node" v-if="formData.nodes.length === 0">
                                <el-dropdown @command="addInputNode">
                                    <el-button type="info">
                                        输入 <i class="el-icon-arrow-down el-icon--right"></i>
                                    </el-button>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item command="input_ds">数据源</el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
                            </div>

                            <template v-for="node, nodeIndex in formData.nodes" :key="nodeIndex">
                                <template  v-if="node.type === 'input'">
                                    <div class="node">
                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'input_ds'">
                                            <el-button @click="toggleNode(node)" type="primary">输入：数据源</el-button>
                                        </div>
                                    </div>
                                </template>

                                <template  v-if="node.type === 'process'">
                                    <div class="node-line"></div>
                                    <div class="node-add">
                                        <el-dropdown @command="addProcessNode">
                                            <el-button type="info" size="mini">
                                                <i class="el-icon-plus"></i>
                                            </el-button>
                                            <el-dropdown-menu slot="dropdown">
                                                <el-dropdown-item :command="'process_code|' + nodeIndex">代码处理</el-dropdown-item>
                                            </el-dropdown-menu>
                                        </el-dropdown>
                                    </div>
                                    <div class="node-line-arrow"></div>
                                    <div class="node node-process" v-if="node.type === 'process'">
                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'process_code'">
                                            <el-button @click="toggleNode(node)" type="warning">代码处理</el-button>
                                        </div>
                                    </div>
                                </template>


                                <template  v-if="node.type === 'output'">
                                    <div class="node-line"></div>
                                    <div class="node-add">
                                        <el-dropdown @command="addProcessNode">
                                            <el-button type="info" size="mini">
                                                <i class="el-icon-plus"></i>
                                            </el-button>
                                            <el-dropdown-menu slot="dropdown">
                                                <el-dropdown-item :command="'process_code|' + nodeIndex">代码处理</el-dropdown-item>
                                            </el-dropdown-menu>
                                        </el-dropdown>
                                    </div>
                                    <div class="node-line-arrow"></div>
                                    <div class="node node-output">
                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'output_ds'">
                                            <el-button @click="toggleNode(node)" type="success">输出：数据源</el-button>
                                        </div>

                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'output_csv'">
                                            <el-button @click="toggleNode(node)" type="success">输出：CSV</el-button>
                                        </div>

                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'output_files'">
                                            <el-button @click="toggleNode(node)" type="success">输出：文件包</el-button>
                                        </div>

                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'output_folders'">
                                            <el-button @click="toggleNode(node)" type="success">输出：目录包</el-button>
                                        </div>

                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'input_api'">
                                            <el-button @click="toggleNode(node)" type="success">输出：API调用</el-button>
                                        </div>
                                    </div>
                                </template>
                            </template>

                            <template v-if="formData.nodes.length > 0 && formData.nodes[formData.nodes.length - 1].type !== 'output'">
                                <div class="node-line"></div>
                                <div class="node-add">
                                    <el-dropdown @command="addProcessNode">
                                        <el-button type="info" size="mini">
                                            <i class="el-icon-plus"></i>
                                        </el-button>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item :command="'process_code|' + formData.nodes.length">代码处理</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </div>
                                <div class="node-line-arrow"></div>
                                <div class="node">
                                    <el-dropdown @command="addOutputNode">
                                        <el-button type="info">
                                            输出 <i class="el-icon-arrow-down el-icon--right"></i>
                                        </el-button>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item command="output_ds">数据源</el-dropdown-item>
                                            <el-dropdown-item command="output_csv">CSV</el-dropdown-item>
                                            <el-dropdown-item command="output_files">文件包</el-dropdown-item>
                                            <el-dropdown-item command="output_folders">目录包</el-dropdown-item>
                                            <el-dropdown-item command="input_api">API调用</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </div>
                            </template>
                            <?php
                            $formData['nodes'] = $this->flow->nodes;
                            ?>
                        </div>
                    </div>
                    <div class="be-col-auto">
                        <div class="be-pl-300 be-pt-200"></div>
                    </div>
                    <div class="be-col-auto" v-if="currentNode">
                        <div style="width:10px; height: 100%; border: #ccc solid 2px; border-right: 0;"></div>
                    </div>
                    <div class="be-col">
                        <div class="be-p-100">
                            <div v-show="currentNode && currentNode.item_type === 'input_ds'">

                                <div class="be-row">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 数据源：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-select v-model="currentNode.ds_id" placeholder="请选择数据源" size="medium" @change="dsChange">
                                            <el-option
                                                    v-for="(name, id) in dsKeyValues"
                                                    :key="id"
                                                    :label="name"
                                                    :value="id">
                                            </el-option>
                                        </el-select>
                                    </div>
                                </div>


                                <div class="be-row be-mt-100">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 类型：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.ds_type" size="medium">
                                            <el-radio-button v-for="(v, k) in dsTypeKeyValues" :label="k">{{v}}</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>


                                <div class="be-row be-mt-100" v-show="currentNode.ds_type === 'table'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 数据表：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-select v-model="currentNode.ds_table" size="medium" placeholder="请选输入数据表" filterable>
                                            <el-option
                                                    v-for="table in dsTables[currentNode.ds_id]"
                                                    :key="table"
                                                    :label="table"
                                                    :value="table">
                                            </el-option>
                                        </el-select>
                                    </div>
                                </div>

                                <div class="be-row be-mt-100" v-show="currentNode.ds_type === 'sql'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> SQL：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <?php
                                        $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                            'name' => 'ds_sql',
                                            'v-model' => 'currentNode.ds_sql',
                                            'language' => 'sql',
                                        ]);
                                        echo $driver->getHtml();
                                        $uiItems->add($driver);
                                        ?>
                                    </div>
                                </div>

                                <div class="be-row be-mt-100">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 断点类型：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.breakpoint" size="medium">
                                            <el-radio-button v-for="(v, k) in breakpointKeyValues" :label="k">{{v}}</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>

                                <div class="be-row be-mt-100" v-if="currentNode.breakpoint === 'breakpoint'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 断点时间：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-date-picker
                                                v-model="currentNode.breakpoint_time"
                                                type="datetime"
                                                size="medium"
                                                placeholder="请选择断点日期时间"
                                                value-format="yyyy-MM-dd HH:mm:ss">
                                        </el-date-picker>
                                    </div>
                                </div>


                                <div class="be-row be-mt-100" v-if="currentNode.breakpoint === 'breakpoint'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 断点递增量：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.breakpoint_step" size="medium">
                                            <el-radio-button v-for="(v, k) in breakpointStepKeyValues" :label="k">{{v}}</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>


                                <div class="be-row be-mt-100" v-if="currentNode.breakpoint === 'breakpoint'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 断点向前编移量：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-input-number v-model="currentNode.breakpoint_offset" size="medium" :step="1"></el-input-number>
                                        <div class="be-mt-50 be-c-999">
                                            此偏移量会将断点范围向前扩充指定的秒数，<br/>
                                            例如：断点为: 2020-09-10 00:00:00，断点递增量：一天, 断点向前编移量 86400 秒。<br/>
                                            计划任务2010-09-11执行时，断点范围为: 2020-09-09 00:00:00 (2020-09-10向前偏移86400秒) <= T < 2020-09-11
                                            00:00:00，即拉取了两天的数据<br/>
                                            计划任务2010-09-12执行时，断点范围为: 2020-09-10 00:00:00 (2020-09-11向前偏移86400秒) <= T < 2020-09-12 00:00:00。
                                        </div>
                                    </div>
                                </div>

                                <div class="be-mt-200 be-bt-eee be-pt-100">
                                    <el-button type="primary" size="medium">验证</el-button>
                                    <el-button type="danger" size="medium" @click="deleteCurrentNode">删除节点</el-button>
                                </div>

                            </div>
                            <div v-if="currentNode && currentNode.item_type === 'input_csv'">


                            </div>



                            <div v-if="currentNode && currentNode.item_type === 'process_clean'">


                            </div>
                            <div v-if="currentNode && currentNode.item_type === 'process_code'">


                            </div>



                            <div v-if="currentNode && currentNode.item_type === 'output_ds'">

                                <div class="be-row">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 数据源：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-select v-model="currentNode.ds_id" placeholder="请选择数据源" @change="dsChange">
                                            <el-option
                                                    v-for="(name, id) in dsKeyValues"
                                                    :key="id"
                                                    :label="name"
                                                    :value="id">
                                            </el-option>
                                        </el-select>
                                    </div>
                                </div>

                                <div class="be-row be-mt-100">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 数据表：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-select v-model="currentNode.ds_table" placeholder="请选输入数据表" filterable>
                                            <el-option
                                                    v-for="table in dsTables[currentNode.ds_id]"
                                                    :key="table"
                                                    :label="table"
                                                    :value="table">
                                            </el-option>
                                        </el-select>
                                    </div>
                                </div>
                                
                            </div>
                            <div v-if="currentNode && currentNode.item_type === 'output_csv'">



                            </div>
                            <div v-if="currentNode && currentNode.item_type === 'output_files'">



                            </div>
                            <div v-if="currentNode && currentNode.item_type === 'output_folders'">



                            </div>
                            <div v-if="currentNode && currentNode.item_type === 'output_api'">



                            </div>

                        </div>
                    </div>
                </div>
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

                currentNode: false,

                categoryKeyValues: <?php echo json_encode($this->categoryKeyValues); ?>,

                dsKeyValues: <?php echo json_encode($this->dsKeyValues); ?>,
                dsTypeKeyValues: <?php echo json_encode($this->dsTypeKeyValues); ?>,
                dsTables: {},

                breakpointKeyValues: <?php echo json_encode($this->breakpointKeyValues); ?>,
                breakpointStepKeyValues: <?php echo json_encode($this->breakpointStepKeyValues); ?>,

                loading: false,
                t: false
                <?php
                echo $uiItems->getVueData();
                ?>
            },
            methods: {

                toggleNode(node) {
                    this.currentNode = node;
                    console.log(node);
                },

                // 添加节点
                addNode(type, itemType, index) {

                    let currentNode = {
                        type: type,
                        item_type: itemType,
                        index: index,
                    };

                    switch (itemType) {
                        case 'input_ds':
                            currentNode.ds_id = '';
                            currentNode.ds_type = 'table';
                            currentNode.ds_table = '';
                            currentNode.ds_sql = '';
                            currentNode.output_fields = [];
                            currentNode.breakpoint = 'full';
                            currentNode.breakpoint_field = '';
                            currentNode.breakpoint_time = '1970-01-02 00:00:00';
                            currentNode.breakpoint_step = '1_DAY';
                            currentNode.breakpoint_offset = 0;
                            break;
                        case 'process_code':
                            currentNode.code = '';
                            currentNode.output_fields = [];
                            break;
                        case 'output_ds':
                            currentNode.ds_id = '';
                            currentNode.ds_table = '';
                            currentNode.field_mapping = 'mapping';
                            currentNode.field_mapping_details = [];
                            currentNode.field_mapping_code = '';
                            break;
                        case 'output_csv':
                            currentNode.field_mapping = [];
                            currentNode.path = '';
                            break;
                        case 'output_files':
                            currentNode.field = '';
                            currentNode.file_ext = '';
                            currentNode.path = '';
                            break;
                        case 'output_api':
                            currentNode.post_url = '';
                            currentNode.post_headers = [];
                            currentNode.post_format = 'form';
                            currentNode.post_data_type = 'mapping';
                            currentNode.post_data_mapping = [];
                            currentNode.post_data_code = '';
                            currentNode.success_mark = '';
                            currentNode.interval = 1000;
                            break;
                    }

                    this.currentNode = currentNode;
                    this.formData.nodes.splice(currentNode.index, 0, currentNode);

                    for(let i in this.formData.nodes) {
                        this.formData.nodes[i].index = i;
                    }
                },
                addInputNode: function (command) {
                    this.addNode('input', command, 0)
                },
                addProcessNode: function (command) {
                    let arr = command.split("|");
                    this.addNode('process', arr[0], arr[1]);
                },
                addOutputNode: function (command) {
                    this.addNode('output', command, this.formData.nodes.length);
                },
                deleteCurrentNode: function () {

                },

                // 验证
                test: function () {

                },

                save: function (command) {
                    let _this = this;
                    this.$refs["formRef"].validate(function (valid) {
                        if (valid) {
                            _this.loading = true;
                            vueNorth.loading = true;
                            _this.$http.post("<?php echo beAdminUrl('Etl.Flow.edit'); ?>", {
                                formData: _this.formData
                            }).then(function (response) {
                                _this.loading = false;
                                vueNorth.loading = false;
                                //console.log(response);
                                if (response.status === 200) {
                                    var responseData = response.data;
                                    if (responseData.success) {
                                        _this.$message.success(responseData.message);

                                        if (command === 'stay') {
                                            _this.formData.id = responseData.process.id;
                                        } else {
                                            setTimeout(function () {
                                                window.onbeforeunload = null;
                                                window.location.href = "<?php echo beAdminUrl('Etl.Flow.index'); ?>";
                                            }, 1000);
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
                                vueNorth.loading = false;
                                _this.$message.error(error);
                            });
                        } else {
                            return false;
                        }
                    });
                },
                cancel: function () {
                    window.onbeforeunload = null;
                    window.location.href = "<?php echo beAdminUrl('Etl.Flow.index'); ?>";
                },

                dsChange: function () {
                    if (this.dsTables[this.currentNode.ds_id] !== undefined) {
                        this.inputTables = this.dsTables[this.currentNode.ds_id];
                    } else {
                        var _this = this;
                        _this.$http.post("<?php echo beAdminUrl('Etl.Ds.getTableNames'); ?>", {
                            dsId: _this.currentNode.ds_id
                        }).then(function (response) {
                            if (response.status === 200) {
                                var responseData = response.data;
                                if (responseData.success) {
                                    _this.dsTables[_this.currentNode.ds_id] = responseData.data.tables;
                                    _this.$forceUpdate();
                                } else {
                                    if (responseData.message) {
                                        _this.$message.error(responseData.message);
                                    }
                                }
                            }
                        }).catch(function (error) {
                            _this.$message.error(error);
                        });
                    }
                },
                loadDsTables: function (dsId, fnSuccess, fnFail) {

                },
                loadInputTableFields: function () {
                    var inputTable = this.formData.input_type === 'table' ? this.formData.input_table : '_sql';

                    if (this.tableFields[this.formData.input_ds_id] !== undefined &&
                        this.tableFields[this.formData.input_ds_id][inputTable] !== undefined) {
                        this.inputTableFields = this.tableFields[this.formData.input_ds_id][inputTable];
                    } else {
                        this.inputTableFieldsLoading = true;

                        var _this = this;
                        var fnSuccess = function () {
                            _this.inputTableFieldsLoading = false;
                            _this.inputTableFields = _this.tableFields[_this.formData.input_ds_id][inputTable];
                            _this.updateFieldMapping();

                            // 生成 CODE
                            if (_this.formData.field_mapping_code === "") {
                                var code = "$return = [];\n";
                                for (var x in _this.fieldMapping) {
                                    if (_this.fieldMapping[x] === "") {
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
                            _this.inputTableFieldsLoading = false;
                        };

                        if (this.formData.input_type === 'table') {
                            this.loadTableFields(this.formData.input_ds_id, this.formData.input_table, fnSuccess, fnFail);
                        } else {
                            this.loadSqlFields(this.formData.input_ds_id, this.formData.input_sql, fnSuccess, fnFail);
                        }
                    }
                },
                loadOutputTableFields: function () {
                    if (this.tableFields[this.formData.output_ds_id] !== undefined &&
                        this.tableFields[this.formData.output_ds_id][this.formData.output_table] !== undefined) {
                        this.outputTableFields = this.tableFields[this.formData.output_ds_id][this.formData.output_table];
                    } else {
                        this.outputTableFieldsLoading = true;
                        var _this = this;
                        this.loadTableFields(this.formData.output_ds_id, this.formData.output_table, function () {
                            _this.outputTableFieldsLoading = false;
                            _this.outputTableFields = _this.tableFields[_this.formData.output_ds_id][_this.formData.output_table];
                            _this.updateFieldMapping();

                            _this.loadSrcTableFields();
                        }, function () {
                            _this.outputTableFieldsLoading = false;
                        });
                    }
                },
                loadSqlFields: function (dsId, sql, fnSuccess, fnFail) {
                    var _this = this;
                    _this.$http.post("<?php echo beAdminUrl('Etl.Ds.getSqlFields'); ?>", {
                        dsId: dsId,
                        sql: sql
                    }).then(function (response) {
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                if (_this.tableFields[dsId] === undefined) {
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
                    _this.$http.post("<?php echo beAdminUrl('Etl.Ds.getTableFields'); ?>", {
                        dsId: dsId,
                        table: table
                    }).then(function (response) {
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                if (_this.tableFields[dsId] === undefined) {
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


                <?php
                echo $uiItems->getVueMethods();
                ?>
            }

            <?php
            $uiItems->setVueHook('mounted', 'window.onbeforeunload = function(e) {e = e || window.event; if (e) { e.returnValue = ""; } return ""; };');
            echo $uiItems->getVueHooks();
            ?>
        });
    </script>
</be-page-content>