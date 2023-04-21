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


<be-north>
    <div id="app-north">
        <div class="be-row">
            <div class="be-col">
                <div style="padding: 1.25rem 0 0 2rem;">
                    <el-link icon="el-icon-back" href="<?php echo beAdminUrl('Etl.Flow.index'); ?>">返回数据流列表</el-link>
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

                        <div class="be-row be-mt-150">
                            <div class="be-col-24 be-md-col-auto be-lh-250">
                                <span class="be-c-red">*</span> 分类：
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
                <div class="be-fs-110">节点配置</div>

                <div class="be-row be-mt-200">
                    <div class="be-col-auto">
                        <div class="nodes">

                            <div class="node" v-if="formData.nodes.length === 0 || formData.nodes[0].type !== 'input'">
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
                                            <el-badge :value="node.item.output === false? '未验证 ' : '已验证'" :type="node.item.output === false? 'danger ' : 'success'">
                                                <el-button @click="toggleNode(node)" type="primary">{{nodeIndex + 1}}. 输入：数据源</el-button>
                                            </el-badge>
                                        </div>
                                    </div>
                                </template>

                                <template v-else>
                                    <div class="node-line"></div>
                                    <div class="node-add">
                                        <el-button type="info" @click="addNodeDialog(nodeIndex)" size="mini">
                                            <i class="el-icon-plus"></i>
                                        </el-button>
                                    </div>
                                    <div class="node-line-arrow"></div>
                                    <div class="node">
                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'process_clean'">
                                            <el-badge :value="node.item.output === false? '未验证 ' : '已验证'" :type="node.item.output === false? 'danger ' : 'success'">
                                                <el-button @click="toggleNode(node)" type="warning">{{nodeIndex + 1}}. 加工：清洗</el-button>
                                            </el-badge>
                                        </div>
                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'process_filter'">
                                            <el-badge :value="node.item.output === false? '未验证 ' : '已验证'" :type="node.item.output === false? 'danger ' : 'success'">
                                                <el-button @click="toggleNode(node)" type="warning">{{nodeIndex + 1}}. 加工：过滤</el-button>
                                            </el-badge>
                                        </div>
                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'process_chatgpt'">
                                            <el-badge :value="node.item.output === false? '未验证 ' : '已验证'" :type="node.item.output === false? 'danger ' : 'success'">
                                                <el-button @click="toggleNode(node)" type="warning">{{nodeIndex + 1}}. 加工：ChatGPT</el-button>
                                            </el-badge>
                                        </div>
                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'process_code'">
                                            <el-badge :value="node.item.output === false? '未验证 ' : '已验证'" :type="node.item.output === false? 'danger ' : 'success'">
                                                <el-button @click="toggleNode(node)" type="warning">{{nodeIndex + 1}}. 加工：代码处理</el-button>
                                            </el-badge>
                                        </div>



                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'output_ds'">
                                            <el-badge :value="node.item.output === false? '未验证 ' : '已验证'" :type="node.item.output === false? 'danger ' : 'success'">
                                                <el-button @click="toggleNode(node)" type="success">{{nodeIndex + 1}}. 输出：数据源</el-button>
                                            </el-badge>
                                        </div>

                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'output_csv'">
                                            <el-badge :value="node.item.output === false? '未验证 ' : '已验证'" :type="node.item.output === false? 'danger ' : 'success'">
                                                <el-button @click="toggleNode(node)" type="success">{{nodeIndex + 1}}. 输出：CSV</el-button>
                                            </el-badge>
                                        </div>

                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'output_files'">
                                            <el-badge :value="node.item.output === false? '未验证 ' : '已验证'" :type="node.item.output === false? 'danger ' : 'success'">
                                                <el-button @click="toggleNode(node)" type="success">{{nodeIndex + 1}}. 输出：文件包</el-button>
                                            </el-badge>
                                        </div>

                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'output_folders'">
                                            <el-badge :value="node.item.output === false? '未验证 ' : '已验证'" :type="node.item.output === false? 'danger ' : 'success'">
                                                <el-button @click="toggleNode(node)" type="success">{{nodeIndex + 1}}. 输出：目录包</el-button>
                                            </el-badge>
                                        </div>

                                        <div :class="{'node-on': currentNode.index == nodeIndex}" v-if="node.item_type === 'output_api'">
                                            <el-badge :value="node.item.output === false? '未验证 ' : '已验证'" :type="node.item.output === false? 'danger ' : 'success'">
                                                <el-button @click="toggleNode(node)" type="success">{{nodeIndex + 1}}. 输出：API调用</el-button>
                                            </el-badge>
                                        </div>
                                    </div>

                                </template>

                            </template>

                            <div class="node-line-arrow"></div>
                            <div class="node">
                                <el-button type="info" @click="addNodeDialog(formData.nodes.length)" size="mini">
                                    <i class="el-icon-plus"></i>
                                </el-button>
                            </div>

                            <?php
                            $formData['nodes'] = $this->flow->nodes;
                            ?>

                            <el-dialog title="添加节点" width="60%" :visible.sync="addNodeDialogVisible" center="true">
                                <div class="be-pb-400">
                                <el-tabs v-model="addNodeDialogTab" type="card">
                                    <el-tab-pane label="加工" name="process">
                                        <el-button type="warning" @click="addNodeDialogConfirm('process', 'process_clean')">清洗</el-button>
                                        <el-button type="warning" @click="addNodeDialogConfirm('process', 'process_filter')">过滤</el-button>
                                        <el-button type="warning" @click="addNodeDialogConfirm('process', 'process_chatgpt')">ChatGPT</el-button>
                                        <el-button type="warning" @click="addNodeDialogConfirm('process', 'process_code')">代码处理</el-button>
                                    </el-tab-pane>
                                    <el-tab-pane label="输出" name="output">
                                        <el-button type="success" @click="addNodeDialogConfirm('output', 'output_ds')">数据源</el-button>
                                        <el-button type="success" @click="addNodeDialogConfirm('output', 'output_csv')">CSV</el-button>
                                        <el-button type="success" @click="addNodeDialogConfirm('output', 'output_files')">文件包</el-button>
                                        <el-button type="success" @click="addNodeDialogConfirm('output', 'output_folders')">目录包</el-button>
                                        <el-button type="success" @click="addNodeDialogConfirm('output', 'output_api')">API调用</el-button>
                                    </el-tab-pane>
                                </el-tabs>
                                </div>
                            </el-dialog>

                        </div>
                    </div>
                    <div class="be-col-auto">
                        <div class="be-pl-400 be-pt-200"></div>
                    </div>
                    <div class="be-col-auto" v-if="currentNode.item">
                        <div style="width:10px; height: 100%; border: #ccc solid 2px; border-right: 0;"></div>
                    </div>
                    <div class="be-col">
                        <div class="be-p-100">



                            <!-- input_ds -->
                            <div v-show="currentNode.item && currentNode.item_type === 'input_ds'">

                                <div class="be-row">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 数据源：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-select v-model="currentNode.item.ds_id" placeholder="请选择数据源" size="medium" @change="dsChange">
                                            <el-option
                                                    v-for="(name, id) in dsKeyValues"
                                                    :key="id"
                                                    :label="name"
                                                    :value="id">
                                            </el-option>
                                        </el-select>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 类型：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.item.ds_type" size="medium">
                                            <el-radio-button v-for="(v, k) in dsTypeKeyValues" :label="k">{{v}}</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-show="currentNode.item.ds_type === 'table'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 数据表：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-select v-model="currentNode.item.ds_table" @change="dsTableChange" size="medium" placeholder="请选输入数据表" filterable>
                                            <el-option
                                                    v-for="table in dsTables[currentNode.item.ds_id]"
                                                    :key="table"
                                                    :label="table"
                                                    :value="table">
                                            </el-option>
                                        </el-select>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150" v-show="currentNode.item.ds_type === 'sql'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> SQL：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <?php
                                        $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                            'name' => 'input_ds_sql',
                                            'language' => 'sql',
                                            //'@change' => 'dsSqlChange',
                                            'ui' => [
                                                'v-model' => 'currentNode.item.ds_sql',
                                            ],
                                        ]);
                                        echo $driver->getHtml();
                                        $uiItems->add($driver);
                                        ?>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 断点类型：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.item.breakpoint" size="medium">
                                            <el-radio-button v-for="(v, k) in breakpointKeyValues" :label="k">{{v}}</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-if="currentNode.item.breakpoint === 'breakpoint'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 断点字段：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-select v-model="currentNode.item.breakpoint_field" v-if="currentNode.item.ds_type === 'table'" placeholder="请选择断点字段">
                                            <el-option v-for="item in inputDsTableFields"
                                                       :key="item.name"
                                                       :label="item.name"
                                                       :value="item.name">
                                            </el-option>
                                        </el-select>

                                        <el-input
                                                type="text"
                                                placeholder="请选择断点字段"
                                                v-model = "currentNode.item.breakpoint_field"
                                                v-if="currentNode.item.ds_type === 'sql'"
                                                size="medium"
                                                style="max-width: 300px;">
                                        </el-input>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-if="currentNode.item.breakpoint === 'breakpoint'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 断点时间：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-date-picker
                                                v-model="currentNode.item.breakpoint_time"
                                                type="datetime"
                                                size="medium"
                                                placeholder="请选择断点日期时间"
                                                value-format="yyyy-MM-dd HH:mm:ss">
                                        </el-date-picker>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150" v-if="currentNode.item.breakpoint === 'breakpoint'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 断点递增量：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.item.breakpoint_step" size="medium">
                                            <el-radio-button v-for="(v, k) in breakpointStepKeyValues" :label="k">{{v}}</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150" v-if="currentNode.item.breakpoint === 'breakpoint'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 断点向前编移量：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-input-number v-model="currentNode.item.breakpoint_offset" size="medium" :step="1"></el-input-number>
                                        <div class="be-mt-50 be-c-999">
                                            此偏移量会将断点范围向前扩充指定的秒数，<br/>
                                            例如：断点为: 2020-09-10 00:00:00，断点递增量：一天, 断点向前编移量 86400 秒。<br/>
                                            计划任务2010-09-11执行时，断点范围为: 2020-09-09 00:00:00 (2020-09-10向前偏移86400秒) <= T < 2020-09-11
                                            00:00:00，即拉取了两天的数据<br/>
                                            计划任务2010-09-12执行时，断点范围为: 2020-09-10 00:00:00 (2020-09-11向前偏移86400秒) <= T < 2020-09-12 00:00:00。
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- input_ds -->




                            <!-- input_csv -->
                            <div v-if="currentNode.item && currentNode.item_type === 'input_csv'">

                            </div>
                            <!-- input_csv -->



                            <!-- process_chatgpt -->
                            <div v-if="currentNode.item && currentNode.item_type === 'process_chatgpt'">

                                <div class="be-row">
                                    <div class="be-col-24 be-md-col-auto">
                                        系统提示语：
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-input
                                                type="textarea"
                                                :autosize="{minRows:4,maxRows:12}"
                                                placeholder="请输入系统提示语"
                                                v-model = "currentNode.item.system_prompt"
                                                size="medium"
                                                maxlength="65535"
                                                show-word-limit>
                                        </el-input>
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-100 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <div class="be-mt-100 be-lh-250">
                                            <span class="be-d-inline-block be-mt-50">插入标签：</span>
                                            <span class="be-d-inline-block be-mt-50" v-for="(v, k) in currentNodeInput">
                                                <el-button @click="processChatgptSystemPromptInsertTag(k)"  type="primary" size="mini" :label="k">{{"{" + k + "}"}}</el-button> &nbsp;
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto">
                                        <span class="be-c-red">*</span> 用户提示语：
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-input
                                                type="textarea"
                                                :autosize="{minRows:4,maxRows:12}"
                                                placeholder="请输入用户提示语"
                                                v-model = "currentNode.item.user_prompt"
                                                size="medium"
                                                maxlength="65535"
                                                show-word-limit>
                                        </el-input>
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-100 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">

                                        <div class="be-mt-100 be-lh-250">
                                            <span class="be-d-inline-block be-mt-50">插入标签：</span>
                                            <span class="be-d-inline-block be-mt-50" v-for="(v, k) in currentNodeInput">
                                                <el-button @click="processChatgptUserPromptInsertTag(k)"  type="primary" size="mini" :label="k">{{"{" + k + "}"}}</el-button> &nbsp;
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 输出字段：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.item.output_field" size="medium">
                                            <el-radio-button label="assign">指定现有字段</el-radio-button>
                                            <el-radio-button label="custom">自定义</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-if="currentNode.item.output_field === 'assign'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 指定输出字段：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <div v-if="currentNodeInput !== false">
                                            <el-select
                                                    v-model="currentNode.item.output_field_assign"
                                                    placeholder="请选择输出字段"
                                                    size="medium"
                                                    filterable>
                                                <el-option
                                                        v-for="(v, k) in currentNodeInput"
                                                        :key="k"
                                                        :label="k"
                                                        :value="k">
                                                </el-option>
                                            </el-select>
                                        </div>
                                        <div class="be-mt-50" v-else>
                                            请先验证上个结点，获取字段列表。
                                        </div>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-if="currentNode.item.output_field === 'custom'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 自定义输出字段：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-input
                                                type="text"
                                                placeholder="请输入自定义字段"
                                                v-model = "currentNode.item.output_field_custom"
                                                size="medium">
                                        </el-input>
                                    </div>
                                </div>

                            </div>
                            <!-- process_chatgpt -->



                            <!-- process_clean -->
                            <div v-if="currentNode.item && currentNode.item_type === 'process_clean'">

                                <div class="be-row">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 清洗字段：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div v-if="currentNodeInput !== false">
                                            <el-select
                                                    v-model="currentNode.item.clean_field"
                                                    placeholder="请选择清洗字段"
                                                    size="medium"
                                                    filterable>
                                                <el-option
                                                        v-for="(v, k) in currentNodeInput"
                                                        :key="k"
                                                        :label="k"
                                                        :value="k">
                                                </el-option>
                                            </el-select>
                                        </div>
                                        <div class="be-mt-50" v-else>
                                            请先验证上个结点，获取字段列表。
                                        </div>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 清洗掉的内容列表：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <el-input
                                                type="textarea"
                                                :autosize="{minRows:20,maxRows:100}"
                                                placeholder="请输入清洗掉的内容"
                                                v-model = "currentNode.item.clean_values"
                                                size="medium"
                                                maxlength="65535"
                                                show-word-limit>
                                        </el-input>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div>一行一个要清洗掉的关键词，</div>
                                        <div class="be-mt-50">如果需要替换，可以一行内用三竖线分隔两个词，例：</div>
                                        <div class="be-c-999 be-mt-50">他的</div>
                                        <div class="be-c-999 be-mt-20">你的|||我的</div>
                                        <div class="be-mt-100">
                                            将执行的操作为：<br>
                                            1. 将 "他的" 剔除掉<br>
                                            2. 将 "你的" 替换为 "我的"
                                        </div>

                                        <div class="be-mt-100">
                                            插入特殊字符：<br>
                                            <span class="be-d-inline-block be-mt-50">
                                                <el-button @click="processCleanCleanValuesInsertTag('换行符')"  type="primary" size="mini">{换行符}</el-button> &nbsp;
                                            </span>
                                        </div>

                                    </div>
                                </div>


                                <div class="be-row be-mt-150">
                                    <div class="be-col-auto">
                                        <span class="be-c-red">*</span> 插入标签：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <div>
                                            <el-switch v-model.number="currentNode.item.insert_tags" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                        </div>

                                        <div v-if="currentNode.item.insert_tags === 1">
                                            <div v-if="currentNodeInput !== false">
                                                <span class="be-d-inline-block be-mt-50"  v-for="(v, k) in currentNodeInput">
                                                    <el-button @click="processCleanCleanValuesInsertTag(k)"  type="primary" size="mini">{{"{" + k + "}"}}</el-button> &nbsp;
                                                </span>
                                            </div>
                                            <div class="be-mt-50" v-else>
                                                请先验证上个结点，获取可插入标签。
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto">
                                        <span class="be-c-red">*</span> 区分大小写：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-switch v-model.number="currentNode.item.match_case" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto">
                                        <span class="be-c-red">*</span> 标记清洗过：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-switch v-model.number="currentNode.item.sign" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-if="currentNode.item.sign === 1">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 标记字段名：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-input
                                                type="text"
                                                placeholder="请输入标记字段名"
                                                v-model = "currentNode.item.sign_field"
                                                size="medium">
                                        </el-input>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150" v-if="currentNode.item.sign === 1">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 标记字段值：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <div class="be-px-100">默认值 - </div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-input
                                                type="text"
                                                placeholder="请输入标记字段值（默认值）"
                                                v-model = "currentNode.item.sign_field_value_0"
                                                size="medium">
                                        </el-input>
                                    </div>
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <div class="be-px-100">已清洗 - </div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-input
                                                type="text"
                                                placeholder="请输入标记字段值（已清洗）"
                                                v-model = "currentNode.item.sign_field_value_1"
                                                size="medium">
                                        </el-input>
                                    </div>
                                </div>



                            </div>
                            <!-- process_clean -->



                            <!-- process_filter -->
                            <div v-if="currentNode.item && currentNode.item_type === 'process_filter'">

                                <div class="be-row">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 过滤字段：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div v-if="currentNodeInput !== false">
                                            <el-select
                                                    v-model="currentNode.item.filter_field"
                                                    placeholder="请选择过滤字段"
                                                    size="medium"
                                                    filterable>
                                                <el-option
                                                        v-for="(v, k) in currentNodeInput"
                                                        :key="k"
                                                        :label="k"
                                                        :value="k">
                                                </el-option>
                                            </el-select>
                                        </div>
                                        <div class="be-mt-50" v-else>
                                            请先验证上个结点，获取字段列表。
                                        </div>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 过滤操作：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <el-select v-model="currentNode.item.filter_op" size="medium">
                                            <el-option label="包含" value="include"></el-option>
                                            <el-option label="以...开头" value="start"></el-option>
                                            <el-option label="以...结尾" value="end"></el-option>
                                            <el-option label="等于" value="eq"></el-option>
                                            <el-option label="大于" value="gt"></el-option>
                                            <el-option label="大于等于" value="gte"></el-option>
                                            <el-option label="小于" value="lt"></el-option>
                                            <el-option label="小于等于" value="lte"></el-option>
                                            <el-option label="范围（过滤值以 ||| 分隔）" value="between"></el-option>
                                        </el-select>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 过滤值列表：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <el-input
                                                type="textarea"
                                                :autosize="{minRows:20,maxRows:100}"
                                                placeholder="请输入系统提示语"
                                                v-model = "currentNode.item.filter_values"
                                                size="medium"
                                                maxlength="65535"
                                                show-word-limit>
                                        </el-input>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div>一行一个关键词，</div>
                                        <div class="be-mt-50">过滤操作为 "范围" 时，在一行内用竖线分隔两个值，例：</div>
                                        <div class="be-c-999 be-mt-50">100|||200</div>
                                        <div class="be-mt-100">
                                            表示范围为：大于等于100，小于等于200
                                        </div>

                                        <div class="be-mt-100">
                                            插入特殊字符：<br>
                                            <span class="be-d-inline-block be-mt-50">
                                                <el-button @click="processFilterFilterValuesInsertTag('换行符')"  type="primary" size="mini">{换行符}</el-button> &nbsp;
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-auto">
                                        <span class="be-c-red">*</span> 插入标签：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <div>
                                            <el-switch v-model.number="currentNode.item.insert_tags" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                        </div>

                                        <div v-if="currentNode.item.insert_tags === 1">
                                            <div v-if="currentNodeInput !== false">
                                                <span class="be-d-inline-block be-mt-50"  v-for="(v, k) in currentNodeInput">
                                                    <el-button @click="processFilterFilterValuesInsertTag(k)"  type="primary" size="mini" :label="k">{{"{" + k + "}"}}</el-button> &nbsp;
                                                </span>
                                            </div>
                                            <div class="be-mt-50" v-else>
                                                请先验证上个结点，获取可插入标签。
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto">
                                        <span class="be-c-red">*</span> 区分大小写：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-switch v-model.number="currentNode.item.match_case" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 操作：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <el-select v-model="currentNode.item.op" size="medium">
                                            <el-option label="符合条件的放行" value="allow"></el-option>
                                            <el-option label="符合条件的中止处理" value="deny"></el-option>
                                        </el-select>
                                    </div>
                                </div>

                            </div>
                            <!-- process_filter -->



                            <!-- process_code -->
                            <div v-show="currentNode.item && currentNode.item_type === 'process_code'">
                                <div class="be-row">
                                    <div class="be-col">
                                        <pre class="be-c-999">function (object $input) ：object {</pre>
                                        <?php
                                        $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                            'name' => 'process_code',
                                            'language' => 'php',
                                            'ui' => [
                                                'v-model' => 'currentNode.item.code',
                                            ],
                                        ]);
                                        echo $driver->getHtml();
                                        $uiItems->add($driver);
                                        ?>
                                        <pre class="be-c-999">}</pre>

                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div class="input-json" v-if="currentNodeInput !== false">
                                            参数 $input 为上个节点输出的数据：
                                            <pre class="be-mt-100 be-c-999">{{JSON.stringify(this.currentNodeInput, null, 4) }}</pre>
                                        </div>
                                        <div v-else>
                                            参数 $input 为上个节点输出的数据，请先验证上个结点，获取其结构。
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- process_code -->



                            <!-- output_ds -->
                            <div v-show="currentNode.item && currentNode.item_type === 'output_ds'">

                                <div class="be-row">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 数据源：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-select v-model="currentNode.item.ds_id" placeholder="请选择数据源" size="medium" @change="dsChange">
                                            <el-option
                                                    v-for="(name, id) in dsKeyValues"
                                                    :key="id"
                                                    :label="name"
                                                    :value="id">
                                            </el-option>
                                        </el-select>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 数据表：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-select v-model="currentNode.item.ds_table" @change="dsTableChange" size="medium"  placeholder="请选输入数据表" filterable>
                                            <el-option
                                                    v-for="table in dsTables[currentNode.item.ds_id]"
                                                    :key="table"
                                                    :label="table"
                                                    :value="table">
                                            </el-option>
                                        </el-select>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 字段处理方式：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.item.field_mapping" size="medium">
                                            <el-radio-button v-for="(v, k) in fieldMappingKeyValues" :label="k">{{v}}</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-show="currentNode.item.field_mapping === 'mapping'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 字段映射：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">

                                        <div class="be-lh-250" v-if="outputDsTableFields.length == 0">
                                            请先选择源据源 & 数据表
                                        </div>
                                        <div v-show="outputDsTableFields.length > 0">

                                            <div style="padding: 5px;">
                                                <el-button @click="outputDsFieldMappingSelectAll" size="mini">全选</el-button>
                                                <el-button @click="outputDsFieldMappingSelectNone" size="mini">全不选</el-button>
                                                <el-button @click="outputDsFieldMappingSelectMatched" size="mini">选中已匹配的</el-button>
                                            </div>


                                            <div class="be-row be-mt-150 field-item-header">
                                                <div class="be-col-auto">
                                                    <div style="width: 50px;">
                                                    </div>
                                                </div>
                                                <div class="be-col">
                                                    <div class="be-pl-100">数据表字段名</div>
                                                </div>
                                                <div class="be-col-auto">
                                                    <div class="be-pl-100"></div>
                                                </div>
                                                <div class="be-col be-ta-center">
                                                    取值类型
                                                </div>
                                                <div class="be-col-auto">
                                                    <div class="be-pl-100"></div>
                                                </div>
                                                <div class="be-col">
                                                    上个节点的输出或自定义
                                                </div>
                                            </div>


                                            <div class="be-row field-item" v-for="mapping, mappingIndex in currentNode.item.field_mapping_details" :key="mappingIndex">

                                                <div class="be-col-auto">
                                                    <div class="be-lh-250 be-ta-center" style="width: 50px;">
                                                        <el-checkbox v-model.number="mapping.enable" :true-label="1" :false-label="0" @change="forceUpdate"></el-checkbox>
                                                    </div>
                                                </div>

                                                <div class="be-col be-lh-250">
                                                    {{mapping.field}}
                                                </div>
                                                <div class="be-col-auto">
                                                    <div class="be-pl-100"></div>
                                                </div>
                                                <div class="be-col be-ta-center be-lh-250">
                                                    <el-radio v-model="mapping.type" label="input_field" :disabled="mapping.enable === 0">取用</el-radio>
                                                    <el-radio v-model="mapping.type" label="custom" :disabled="mapping.enable === 0">自定义</el-radio>
                                                </div>
                                                <div class="be-col-auto">
                                                    <div class="be-pl-100"></div>
                                                </div>
                                                <div class="be-col">
                                                    <div v-show="mapping.type === 'input_field'">
                                                        <el-select
                                                                v-model="mapping.input_field"
                                                                @change="forceUpdate"
                                                                :disabled="mapping.enable === 0"
                                                                placeholder="请选择输入字段"
                                                                size="medium"
                                                                filterable>
                                                            <el-option
                                                                    v-for="(v, k) in currentNodeInput"
                                                                    :key="k"
                                                                    :label="k"
                                                                    :value="k">
                                                            </el-option>
                                                        </el-select>
                                                    </div>
                                                    <div v-show="mapping.type === 'custom'">
                                                        <el-input
                                                                type="text"
                                                                placeholder="请输入自定义值"
                                                                v-model = "mapping.custom"
                                                                :disabled="mapping.enable === 0"
                                                                size="medium">
                                                        </el-input>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                                <div class="be-row be-mt-150" v-show="currentNode.item.field_mapping === 'code'">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 代码处理：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <pre class="be-c-999">function (object $input) ：object {</pre>
                                        <?php
                                        $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                            'name' => 'output_ds_field_mapping_code',
                                            'language' => 'php',
                                            'ui' => [
                                                'v-model' => 'currentNode.item.field_mapping_code',
                                            ],
                                        ]);
                                        echo $driver->getHtml();
                                        $uiItems->add($driver);
                                        ?>
                                        <pre class="be-c-999">}</pre>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div class="input-json" v-if="currentNodeInput !== false">
                                            参数 $input 为上个节点输出的数据：
                                            <pre class="be-mt-100 be-c-999">{{JSON.stringify(this.currentNodeInput, null, 4) }}</pre>
                                        </div>
                                        <div v-else>
                                            参数 $input 为上个节点输出的数据，请先验证上个结点，获取其结构。
                                        </div>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 数据操作类型：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-select v-model="currentNode.item.op" size="medium">
                                            <el-option label="插入，重复数据更新" value="auto"></el-option>
                                            <el-option label="插入" value="insert"></el-option>
                                            <el-option label="更新" value="update"></el-option>
                                            <el-option label="删除" value="delete"></el-option>
                                        </el-select>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-if="currentNode.item.op === 'auto' || currentNode.item.op === 'update' || currentNode.item.op === 'delete'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 更新/删除操作的唯一键字段：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-select
                                                v-model="currentNode.item.op_field"
                                                placeholder="请选择字段"
                                                size="medium"
                                                filterable>
                                            <el-option
                                                    v-for="field in outputDsTableFields"
                                                    :key="field.name"
                                                    :label="field.name"
                                                    :value="field.name">
                                            </el-option>
                                        </el-select>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-if="currentNode.item.op === 'auto'">
                                    <div class="be-col-24 be-md-col-auto">
                                        <span class="be-c-red">*</span> 启用 MYSQL 数据库 Replace Into：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-switch v-model.number="currentNode.item.mysql_replace" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                    </div>
                                </div>



                                <div class="be-row be-mt-150" v-if="currentNode.item && currentNode.item_type === 'output_ds'">
                                    <div class="be-col-24 be-md-col-auto">
                                        <span class="be-c-red">*</span> 运行前清空数据表（如：全量同步时）：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-switch v-model.number="currentNode.item.clean" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150" v-if="currentNode.item.clean === 1">
                                    <div class="be-col-24 be-md-col-auto">
                                        <span class="be-c-red">*</span> 清空数据表方式：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio v-model="currentNode.item.clean_type" label="truncate">TRUNCATE</el-radio>
                                        <el-radio v-model="currentNode.item.clean_type" label="delete">DELETE</el-radio>
                                    </div>
                                </div>

                            </div>
                            <!-- output_ds -->




                            <!-- output_csv -->
                            <div v-show="currentNode.item && currentNode.item_type === 'output_csv'">

                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 列处理方式：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.item.field_mapping" size="medium">
                                            <el-radio-button v-for="(v, k) in fieldMappingKeyValues" :label="k">{{v}}</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150" v-show="currentNode.item.field_mapping === 'mapping'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 字段映射：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">

                                        <div class="be-row be-mt-150 field-item-header">
                                            <div class="be-col">
                                                <div class="be-pl-100">列名</div>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-100"></div>
                                            </div>
                                            <div class="be-col be-ta-center">
                                                取值类型
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-100"></div>
                                            </div>
                                            <div class="be-col">
                                                上个节点的输出或自定义
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="field-item-op">
                                                    操作
                                                </div>
                                            </div>
                                        </div>

                                        <div class="be-row field-item" v-for="mapping, mappingIndex in currentNode.item.field_mapping_details" :key="mappingIndex">
                                            <div class="be-col">
                                                <el-input
                                                        type="text"
                                                        placeholder="请输入列名"
                                                        v-model = "mapping.field"
                                                        size="medium"
                                                        maxlength="300"
                                                        show-word-limit>
                                                </el-input>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-100"></div>
                                            </div>
                                            <div class="be-col be-ta-center be-lh-250">
                                                <el-radio v-model="mapping.type" label="input_field">取用</el-radio>
                                                <el-radio v-model="mapping.type" label="custom">自定义</el-radio>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-100"></div>
                                            </div>
                                            <div class="be-col">
                                                <div v-show="mapping.type === 'input_field'">
                                                    <el-select
                                                            v-model="mapping.input_field"
                                                            @change="forceUpdate"
                                                            placeholder="请选择输入字段"
                                                            size="medium"
                                                            filterable>
                                                        <el-option
                                                                v-for="(v, k) in currentNodeInput"
                                                                :key="k"
                                                                :label="k"
                                                                :value="k">
                                                        </el-option>
                                                    </el-select>
                                                </div>
                                                <div v-show="mapping.type === 'custom'">
                                                    <el-input
                                                            type="text"
                                                            placeholder="请输入自定义值"
                                                            v-model = "mapping.custom"
                                                            size="medium">
                                                    </el-input>
                                                </div>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="field-item-op">
                                                    <el-link type="danger" icon="el-icon-delete" @click="outputCsvFieldMappingDelete(mapping)"></el-link>
                                                </div>
                                            </div>
                                        </div>

                                        <el-button class="be-mt-100" size="small" type="primary" @click="outputCsvFieldMappingAdd">新增列</el-button>

                                    </div>
                                </div>

                                <div class="be-row be-mt-150" v-show="currentNode.item.field_mapping === 'code'">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 代码处理：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <pre class="be-c-999">function (object $input) ：object {</pre>
                                        <?php
                                        $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                            'name' => 'output_csv_field_mapping_code',
                                            'language' => 'php',
                                            'ui' => [
                                                'v-model' => 'currentNode.item.field_mapping_code',
                                            ],
                                        ]);
                                        echo $driver->getHtml();
                                        $uiItems->add($driver);
                                        ?>
                                        <pre class="be-c-999">}</pre>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div class="input-json" v-if="currentNodeInput !== false">
                                            参数 $input 为上个节点输出的数据：
                                            <pre class="be-mt-100 be-c-999">{{JSON.stringify(this.currentNodeInput, null, 4) }}</pre>
                                        </div>
                                        <div v-else>
                                            参数 $input 为上个节点输出的数据，请先验证上个结点，获取其结构。
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- output_csv -->


                            <!-- output_files -->
                            <div v-show="currentNode.item && currentNode.item_type === 'output_files'">

                                <div class="be-row">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 文件名处理方式：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.item.name" size="medium">
                                            <el-radio-button label="template">命名模板</el-radio-button>
                                            <el-radio-button label="code">代码处理</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150" v-show="currentNode.item.name === 'template'">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 文件名称模板：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div>
                                            <el-input
                                                    type="text"
                                                    placeholder="请输入文件名称模板（例:{id}.txt）"
                                                    v-model = "currentNode.item.name_template"
                                                    size="medium">
                                            </el-input>
                                        </div>
                                        <div v-if="currentNodeInput !== false">
                                            <span class="be-d-inline-block be-mt-50">插入标签：</span>
                                            <span class="be-d-inline-block be-mt-50"  v-for="(v, k) in currentNodeInput">
                                                <el-button @click="outputFilesNameInsertTag(k)"  type="primary" size="mini" :label="k">{{"{" + k + "}"}}</el-button> &nbsp;
                                            </span>
                                        </div>
                                        <div class="be-mt-50" v-else>
                                            请先验证上个结点，获取可插入标签。
                                        </div>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-show="currentNode.item.name === 'code'">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 代码处理：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <pre class="be-c-999">function (object $input) ：string {</pre>
                                        <?php
                                        $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                            'name' => 'output_files_name_code',
                                            'language' => 'php',
                                            'ui' => [
                                                'v-model' => 'currentNode.item.name_code',
                                            ],
                                        ]);
                                        echo $driver->getHtml();
                                        $uiItems->add($driver);
                                        ?>
                                        <pre class="be-c-999">}</pre>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div class="input-json" v-if="currentNodeInput !== false">
                                            参数 $input 为上个节点输出的数据：
                                            <pre class="be-mt-100 be-c-999">{{JSON.stringify(this.currentNodeInput, null, 4) }}</pre>
                                        </div>
                                        <div v-else>
                                            参数 $input 为上个节点输出的数据，请先验证上个结点，获取其结构。
                                        </div>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 内容处理方式：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.item.content" size="medium">
                                            <el-radio-button label="template">内容模板</el-radio-button>
                                            <el-radio-button label="code">代码处理</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-show="currentNode.item.content === 'template'">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 文件内容模板：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div>
                                            <el-input
                                                    type="textarea"
                                                    placeholder="请输入文件内容模板"
                                                    v-model = "currentNode.item.content_template"
                                                    size="medium">
                                            </el-input>
                                        </div>
                                        <div v-if="currentNodeInput !== false">
                                            <span class="be-d-inline-block be-mt-50">插入标签：</span>
                                            <span class="be-d-inline-block be-mt-50"  v-for="(v, k) in currentNodeInput">
                                                <el-button @click="outputFilesContentInsertTag(k)" type="primary" size="mini" :label="k">{{"{" + k + "}"}}</el-button> &nbsp;
                                            </span>
                                        </div>
                                        <div class="be-mt-50" v-else>
                                            请先验证上个结点，获取可插入标签。
                                        </div>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-show="currentNode.item.content === 'code'">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 代码处理：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <pre class="be-c-999">function (object $input) ：string {</pre>
                                        <?php
                                        $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                            'name' => 'output_files_content_code',
                                            'language' => 'php',
                                            'ui' => [
                                                'v-model' => 'currentNode.item.content_code',
                                            ],
                                        ]);
                                        echo $driver->getHtml();
                                        $uiItems->add($driver);
                                        ?>
                                        <pre class="be-c-999">}</pre>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div class="input-json" v-if="currentNodeInput !== false">
                                            参数 $input 为上个节点输出的数据：
                                            <pre class="be-mt-100 be-c-999">{{JSON.stringify(this.currentNodeInput, null, 4) }}</pre>
                                        </div>
                                        <div v-else>
                                            参数 $input 为上个节点输出的数据，请先验证上个结点，获取其结构。
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- output_files -->



                            <!-- output_folders -->
                            <div v-show="currentNode.item && currentNode.item_type === 'output_folders'">

                                <div class="be-row">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 目录名处理方式：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-radio-group v-model="currentNode.item.name" size="medium">
                                            <el-radio-button label="template">命名模板</el-radio-button>
                                            <el-radio-button label="code">代码处理</el-radio-button>
                                        </el-radio-group>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150" v-show="currentNode.item.name === 'template'">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 目录名模板：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div>
                                            <el-input
                                                    type="text"
                                                    placeholder="请输入文件名称模板（例:{id}）"
                                                    v-model = "currentNode.item.name_template"
                                                    size="medium">
                                            </el-input>
                                        </div>
                                        <div v-if="currentNodeInput !== false">
                                            <span class="be-d-inline-block be-mt-50">插入标签：</span>
                                            <span class="be-d-inline-block be-mt-50" v-for="(v, k) in currentNodeInput">
                                                <el-button @click="outputFoldersNameInsertTag(k)"  type="primary" size="mini" :label="k">{{"{" + k + "}"}}</el-button> &nbsp;
                                            </span>
                                        </div>
                                        <div class="be-mt-50" v-else>
                                            请先验证上个结点，获取可插入标签。
                                        </div>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-show="currentNode.item.name === 'code'">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 代码处理：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <pre class="be-c-999">function (object $input) ：string {</pre>
                                        <?php
                                        $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                            'name' => 'output_folders_name_code',
                                            'language' => 'php',
                                            'ui' => [
                                                'v-model' => 'currentNode.item.name_code',
                                            ],
                                        ]);
                                        echo $driver->getHtml();
                                        $uiItems->add($driver);
                                        ?>
                                        <pre class="be-c-999">}</pre>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div class="input-json" v-if="currentNodeInput !== false">
                                            参数 $input 为上个节点输出的数据：
                                            <pre class="be-mt-100 be-c-999">{{JSON.stringify(this.currentNodeInput, null, 4) }}</pre>
                                        </div>
                                        <div v-else>
                                            参数 $input 为上个节点输出的数据，请先验证上个结点，获取其结构。
                                        </div>
                                    </div>
                                </div>



                                <div class="be-mt-200 be-fw-bold be-bb-eee be-pb-50 be-mb-100">文件列表</div>

                                <div class="be-row be-mb-300" v-for="file, fileIndex in currentNode.item.files" :key="fileIndex">
                                    <div class="be-col-auto be-lh-250">
                                        文件 {{fileIndex+1}}：
                                    </div>
                                    <div class="be-col">

                                        <div class="be-row">
                                            <div class="be-col-auto be-lh-250">
                                                <span class="be-c-red">*</span> 文件名称模板：
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-50 be-pt-100"></div>
                                            </div>
                                            <div class="be-col">
                                                <div>
                                                    <el-input
                                                            type="text"
                                                            placeholder="请输入文件名称模板（例:{id}.txt）"
                                                            v-model = "file.name_template"
                                                            size="medium">
                                                    </el-input>
                                                </div>
                                                <div v-if="currentNodeInput !== false">
                                                    <span class="be-d-inline-block be-mt-50">插入标签：</span>
                                                    <span class="be-d-inline-block be-mt-50"  v-for="(v, k) in currentNodeInput">
                                                <el-button @click="outputFoldersFileNameInsertTag(k, fileIndex)"  type="primary" size="mini" :label="k">{{"{" + k + "}"}}</el-button> &nbsp;
                                            </span>
                                                </div>
                                                <div class="be-mt-50" v-else>
                                                    请先验证上个结点，获取可插入标签。
                                                </div>
                                            </div>
                                        </div>


                                        <div class="be-row be-mt-150">
                                            <div class="be-col-auto be-lh-250">
                                                <span class="be-c-red">*</span> 文件内容模板：
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-50 be-pt-100"></div>
                                            </div>
                                            <div class="be-col">
                                                <div>
                                                    <el-input
                                                            type="textarea"
                                                            placeholder="请输入文件内容模板"
                                                            v-model = "file.content_template"
                                                            size="medium">
                                                    </el-input>
                                                </div>
                                                <div v-if="currentNodeInput !== false">
                                                    <span class="be-d-inline-block be-mt-50">插入标签：</span>
                                                    <span class="be-d-inline-block be-mt-50"  v-for="(v, k) in currentNodeInput">
                                                        <el-button @click="outputFoldersFileContentInsertTag(k, fileIndex)" type="primary" size="mini" :label="k">{{"{" + k + "}"}}</el-button> &nbsp;
                                                    </span>
                                                </div>
                                                <div class="be-mt-50" v-else>
                                                    请先验证上个结点，获取可插入标签。
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-p-50">
                                            <el-link type="danger" icon="el-icon-delete" @click="outputFoldersFilesDelete(file)"></el-link>
                                        </div>

                                    </div>
                                </div>

                                <el-button class="be-mt-100" size="small" type="primary" @click="outputFoldersFilesAdd">新增文件</el-button>

                                <div class="be-mt-200 be-bb-eee be-pb-50 be-mb-100"><span class="be-fw-bold">代码输出文件列表</span><span class="be-c-999">（键值对，键名 - 文件名，键值 - 文件内容）</span></div>
                                <div class="be-row be-mt-150">
                                    <div class="be-col">
                                        <pre class="be-c-999">function (object $input) ：array {</pre>
                                        <?php
                                        $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                            'name' => 'output_folders_files_code',
                                            'language' => 'php',
                                            'ui' => [
                                                'v-model' => 'currentNode.item.files_code',
                                            ],
                                        ]);
                                        echo $driver->getHtml();
                                        $uiItems->add($driver);
                                        ?>
                                        <pre class="be-c-999">}</pre>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div class="input-json" v-if="currentNodeInput !== false">
                                            参数 $input 为上个节点输出的数据：
                                            <pre class="be-mt-100 be-c-999">{{JSON.stringify(this.currentNodeInput, null, 4) }}</pre>
                                        </div>
                                        <div v-else>
                                            参数 $input 为上个节点输出的数据，请先验证上个结点，获取其结构。
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- output_folders -->



                            <!-- output_api  -->
                            <div v-show="currentNode.item && currentNode.item_type === 'output_api'">

                                <div class="be-row">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> API网址：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-input
                                                type="text"
                                                placeholder="请输入API网址"
                                                v-model="currentNode.item.url"
                                                maxlength="300"
                                                show-word-limit>
                                        </el-input>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-auto be-lh-250">
                                        请求头：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">

                                        <div class="be-row field-item-header">
                                            <div class="be-col">
                                                <div class="be-pl-100">名称</div>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-100"></div>
                                            </div>
                                            <div class="be-col">
                                                值
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="field-item-op">
                                                    操作
                                                </div>
                                            </div>
                                        </div>


                                        <div class="be-row field-item" v-for="header, headerIndex in currentNode.item.headers" :key="headerIndex">
                                            <div class="be-col">
                                                <el-input
                                                        type="text"
                                                        placeholder="请输入名称"
                                                        v-model = "header.name"
                                                        size="medium"
                                                        maxlength="300"
                                                        show-word-limit>
                                                </el-input>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-100"></div>
                                            </div>
                                            <div class="be-col">
                                                <el-input
                                                        type="text"
                                                        placeholder="请输入值"
                                                        v-model = "header.value"
                                                        size="medium"
                                                        maxlength="600"
                                                        show-word-limit>
                                                </el-input>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="field-item-op">
                                                    <el-link type="danger" icon="el-icon-delete" @click="outputApiDeleteHeader(header)"></el-link>
                                                </div>
                                            </div>
                                        </div>

                                        <el-button class="be-mt-100" size="small" type="primary" @click="outputApiAddHeader">新增请求头</el-button>

                                    </div>
                                </div>


                                <div class="be-row be-mt-150">
                                    <div class="be-col-auto">
                                        <span class="be-c-red">*</span> 请求格式：
                                    </div>
                                    <div class="be-col">
                                        <el-radio v-model="currentNode.item.format" label="form">FORM 表单</el-radio>
                                        <el-radio v-model="currentNode.item.format" label="json">JSON 数据</el-radio>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-auto">
                                        <span class="be-c-red">*</span> 清求体处理：
                                    </div>
                                    <div class="be-col">
                                        <el-radio v-model="currentNode.item.field_mapping" label="mapping">映射</el-radio>
                                        <el-radio v-model="currentNode.item.field_mapping" label="code">代码处理</el-radio>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150" v-show="currentNode.item.field_mapping === 'mapping'">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 字段映射：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">

                                        <div class="be-row field-item-header">
                                            <div class="be-col">
                                                <div class="be-pl-100">列名</div>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-100"></div>
                                            </div>
                                            <div class="be-col be-ta-center">
                                                取值类型
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-100"></div>
                                            </div>
                                            <div class="be-col">
                                                上个节点的输出或自定义
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="field-item-op">
                                                    操作
                                                </div>
                                            </div>
                                        </div>

                                        <div class="be-row field-item" v-for="mapping, mappingIndex in currentNode.item.field_mapping_details" :key="mappingIndex">
                                            <div class="be-col">
                                                <el-input
                                                        type="text"
                                                        placeholder="请输入列名"
                                                        v-model = "mapping.field"
                                                        size="medium"
                                                        maxlength="300"
                                                        show-word-limit>
                                                </el-input>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-100"></div>
                                            </div>
                                            <div class="be-col be-ta-center be-lh-250">
                                                <el-radio v-model="mapping.type" label="input_field">取用</el-radio>
                                                <el-radio v-model="mapping.type" label="custom">自定义</el-radio>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="be-pl-100"></div>
                                            </div>
                                            <div class="be-col">
                                                <div v-show="mapping.type === 'input_field'">
                                                    <el-select
                                                            v-model="mapping.input_field"
                                                            @change="forceUpdate"
                                                            placeholder="请选择输入字段"
                                                            size="medium"
                                                            filterable>
                                                        <el-option
                                                                v-for="(v, k) in currentNodeInput"
                                                                :key="k"
                                                                :label="k"
                                                                :value="k">
                                                        </el-option>
                                                    </el-select>
                                                </div>
                                                <div v-show="mapping.type === 'custom'">
                                                    <el-input
                                                            type="text"
                                                            placeholder="请输入自定义值"
                                                            v-model = "mapping.custom"
                                                            size="medium">
                                                    </el-input>
                                                </div>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="field-item-op">
                                                    <el-link type="danger" icon="el-icon-delete" @click="outputApiFieldMappingDelete(mapping)"></el-link>
                                                </div>
                                            </div>
                                        </div>

                                        <el-button class="be-mt-100" size="small" type="primary" @click="outputApiFieldMappingAdd">新增字段</el-button>

                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-show="currentNode.item.field_mapping === 'code'">
                                    <div class="be-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 代码处理：
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <pre class="be-c-999">function (object $input) ：object {</pre>
                                        <?php
                                        $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                            'name' => 'output_api_field_mapping_code',
                                            'language' => 'php',
                                            'ui' => [
                                                'v-model' => 'currentNode.item.field_mapping_code',
                                            ],
                                        ]);
                                        echo $driver->getHtml();
                                        $uiItems->add($driver);
                                        ?>
                                        <pre class="be-c-999">}</pre>
                                    </div>
                                    <div class="be-col-auto">
                                        <div class="be-pl-100"></div>
                                    </div>
                                    <div class="be-col">
                                        <div class="input-json" v-if="currentNodeInput !== false">
                                            参数 $input 为上个节点输出的数据：
                                            <pre class="be-mt-100 be-c-999">{{JSON.stringify(this.currentNodeInput, null, 4) }}</pre>
                                        </div>
                                        <div v-else>
                                            参数 $input 为上个节点输出的数据，请先验证上个结点，获取其结构。
                                        </div>
                                    </div>
                                </div>


                                <div class="be-row be-mt-150" v-if="currentNode.item">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 间隔时间（毫秒）：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-input-number v-model="currentNode.item.interval"></el-input-number>
                                    </div>
                                </div>

                                <div class="be-row be-mt-150">
                                    <div class="be-col-24 be-md-col-auto be-lh-250">
                                        <span class="be-c-red">*</span> 成功标记：
                                    </div>
                                    <div class="be-col-24 be-md-col-auto">
                                        <div class="be-pl-50 be-pt-100"></div>
                                    </div>
                                    <div class="be-col-24 be-md-col">
                                        <el-input
                                                type="text"
                                                placeholder="请输入成功标记"
                                                v-model = "currentNode.item.success_mark"
                                                size="medium"
                                                maxlength="60"
                                                show-word-limit>
                                        </el-input>
                                    </div>
                                </div>

                            </div>
                            <!-- output_api   -->


                            <div class="be-mt-200 be-bt-eee be-pt-100" v-show="currentNode.item">
                                <el-button type="success" size="medium" :disabled="loading" @click="test">验证</el-button>
                                <el-button type="danger" size="medium" :disabled="loading" @click="deleteCurrentNode">删除节点</el-button>
                            </div>

                            <div class="be-mt-200" v-show="currentNode.item && currentNode.item.output">
                                <el-alert title="验证通过，此节点输出如下：" type="success" :closable="false" show-icon></el-alert>
                                <div class="be-mt-100">
                                    <?php
                                    $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                        'name' => 'current_node_output',
                                        'language' => 'json',
                                    ]);
                                    echo $driver->getHtml();
                                    $uiItems->add($driver);
                                    ?>
                                </div>
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

                categoryKeyValues: <?php echo json_encode($this->categoryKeyValues); ?>,

                addNodeIndex: 0,
                addNodeDialogVisible: false,
                addNodeDialogTab: "process",

                currentNode: {item: false,},
                currentNodeInput: false,

                dsKeyValues: <?php echo json_encode($this->dsKeyValues); ?>,
                dsTypeKeyValues: <?php echo json_encode($this->dsTypeKeyValues); ?>,
                dsTables: {},
                dsTableFields: {},

                breakpointKeyValues: <?php echo json_encode($this->breakpointKeyValues); ?>,
                breakpointStepKeyValues: <?php echo json_encode($this->breakpointStepKeyValues); ?>,
                fieldMappingKeyValues: <?php echo json_encode($this->fieldMappingKeyValues); ?>,


                // 输入 表字段
                inputDsTableFields: [],

                // 输出 表字段
                outputDsTableFields: [],

                loading: false,

                t: false
                <?php
                echo $uiItems->getVueData();
                ?>
            },
            methods: {

                toggleNode(node) {

                    this.currentNode = node;

                    //console.log(this.currentNode);

                    if (node.index === 0) {
                        this.currentNodeInput = false;
                    } else {

                        let tmpNodeId = node.index-1;
                        while (tmpNodeId > 0 && this.formData.nodes[tmpNodeId].item.type === "output") {
                            tmpNodeId--;
                        }

                        this.currentNodeInput = this.formData.nodes[tmpNodeId].item.output;
                    }

                    switch (node.item_type) {
                        case 'input_ds':

                            if (this.currentNode.item.ds_id !== "" && !this.dsTables.hasOwnProperty(this.currentNode.item.ds_id)) {
                                this.dsChange();
                            }

                            if (this.currentNode.item.ds_table !== "" && !this.dsTableFields.hasOwnProperty(this.currentNode.item.ds_table)) {
                                this.dsTableChange();
                            }

                            this.formItems.input_ds_sql.codeMirror.setValue(this.currentNode.item.ds_sql);

                            break;

                        case 'process_code':
                            this.formItems.process_code.codeMirror.setValue(this.currentNode.item.code);
                            break;


                        case 'output_ds':

                            if (this.currentNode.item.ds_id !== "" && !this.dsTables.hasOwnProperty(this.currentNode.item.ds_id)) {
                                this.dsChange();
                            }

                            if (this.currentNode.item.ds_table !== "" && !this.dsTableFields.hasOwnProperty(this.currentNode.item.ds_table)) {
                                this.dsTableChange();
                            }

                            this.outputDsUpdateFieldMapping();
                            break;
                        case 'output_csv':
                            this.formItems.output_csv_field_mapping_code.codeMirror.setValue(this.currentNode.item.field_mapping_code);
                            this.outputCsvUpdateFieldMapping();
                            break;
                        case 'output_files':
                            this.formItems.output_files_name_code.codeMirror.setValue(this.currentNode.item.name_code);
                            this.formItems.output_files_content_code.codeMirror.setValue(this.currentNode.item.content_code);
                            break;
                        case 'output_folders':
                            this.formItems.output_folders_name_code.codeMirror.setValue(this.currentNode.item.name_code);
                            this.formItems.output_folders_files_code.codeMirror.setValue(this.currentNode.item.files_code);
                            break;
                        case 'output_api':
                            this.formItems.output_api_field_mapping_code.codeMirror.setValue(this.currentNode.item.field_mapping_code);
                            this.outputApiUpdateFieldMapping();
                            break;
                    }

                    //console.log(this.currentNode);

                    if (this.currentNode.item.output !== false) {
                        this.formItems.current_node_output.codeMirror.setValue(JSON.stringify(this.currentNode.item.output, null, 4));
                    }

                    this.$forceUpdate();
                },

                addInputNode: function (command) {
                    this.addNode('input', command, 0)
                },
                addNodeDialog(index) {
                    this.addNodeIndex = Number(index);
                    this.addNodeDialogVisible = true;
                },
                addNodeDialogConfirm(type, itemType) {
                    this.addNodeDialogVisible = false;
                    this.addNode(type, itemType, this.addNodeIndex);
                    this.addNodeIndex = 0;
                },
                // 添加节点
                addNode(type, itemType, index) {

                    let currentNode = {
                        type: type,
                        item_type: itemType,
                        index: Number(index),
                    };

                    let item = {};

                    switch (itemType) {
                        case 'input_ds':
                            item.ds_id = '';
                            item.ds_type = 'table';
                            item.ds_table = '';
                            item.ds_sql = '';
                            item.breakpoint = 'full';
                            item.breakpoint_field = '';
                            item.breakpoint_time = '1970-01-02 00:00:00';
                            item.breakpoint_step = '1_DAY';
                            item.breakpoint_offset = 0;
                            item.output = false;
                            break;


                        case 'process_clean':
                            item.clean_field = '';
                            item.clean_values = '';
                            item.match_case = 0;
                            item.insert_tags = 0;
                            item.sign = 0;
                            item.sign_field = 'cleaned';
                            item.sign_field_value_0 = '0';
                            item.sign_field_value_1 = '1';
                            item.op = 'allow';
                            item.output = false;
                            break;
                        case 'process_filter':
                            item.filter_field = '';
                            item.filter_op = 'include';
                            item.filter_values = '';
                            item.match_case = 0;
                            item.insert_tags = 0;
                            item.op = 'allow';
                            item.output = false;
                            break;
                        case 'process_chatgpt':
                            item.system_prompt = '';
                            item.user_prompt = '';
                            item.output_field = 'assign';
                            item.output_field_assign = '';
                            item.output_field_custom = '';
                            item.output = false;
                            break;
                        case 'process_code':
                            item.code = 'return $input;';
                            item.output = false;
                            break;


                        case 'output_ds':
                            item.ds_id = '';
                            item.ds_table = '';
                            item.field_mapping = 'mapping';
                            item.field_mapping_details = [];
                            item.field_mapping_code = '';
                            item.op = 'auto';
                            item.op_field = 'id';
                            item.mysql_replace = 1;
                            item.clean = 1;
                            item.clean_type = 'truncate';
                            item.output = false;
                            break;
                        case 'output_csv':
                            item.field_mapping = 'mapping';
                            item.field_mapping_details = [];
                            item.field_mapping_code = '';
                            item.output = false;
                            break;
                        case 'output_files':
                            item.name = 'template';
                            item.name_template = '';
                            item.name_code = '';
                            item.content = 'template';
                            item.content_template = '';
                            item.content_code = '';
                            item.output = false;
                            break;
                        case 'output_folders':
                            item.name = 'template';
                            item.name_template = '';
                            item.name_code = '';
                            item.files = [];
                            item.files_code = '';
                            item.output = false;
                            break;
                        case 'output_api':
                            item.url = '';
                            item.headers = [];
                            item.format = 'form';
                            item.field_mapping = 'mapping';
                            item.field_mapping_details = [];
                            item.field_mapping_code = '';
                            item.interval = 1000;
                            item.success_mark = '';
                            item.output = false;
                            break;
                    }

                    currentNode.item = item;

                    this.formData.nodes.splice(currentNode.index, 0, currentNode);

                    // 更新节点间的输入和办理出
                    for(let i in this.formData.nodes) {
                        if (i < index) {
                            continue;
                        }

                        let node = this.formData.nodes[i];

                        node.index = Number(i);
                        node.item.output = false;
                    }

                    this.toggleNode(currentNode);
                },
                deleteCurrentNode: function () {

                    // 清理节点数据
                    switch (this.currentNode.item_type) {
                        case 'input_ds':
                            this.inputDsTableFields = [];
                            break;

                        case 'process_code':
                            break;
                        case 'process_chatgpt':
                            break;

                        case 'output_ds':
                            this.outputDsTableFields = [];
                            break;
                        case 'output_csv':
                            break;
                        case 'output_files':
                            break;
                        case 'output_folders':
                            break;
                        case 'output_api':
                            break;
                    }

                    let currentIndex = this.currentNode.index;

                    this.formData.nodes.splice(this.currentNode.index, 1);
                    this.currentNode = {item: false};

                    // 更新节点间的输入和办理出
                    for(let i in this.formData.nodes) {
                        if (i < currentIndex) {
                            continue;
                        }

                        let node = this.formData.nodes[i];
                        node.index = Number(i);
                        node.item.output = false;
                    }

                    this.$forceUpdate();
                },

                // 验证
                test: function () {
                    this.loading = true;
                    vueNorth.loading = true;

                    let _this = this;
                    this.$http.post("<?php echo beAdminUrl('Etl.Flow.test'); ?>", {
                        formData: _this.formData,
                        index: _this.currentNode.index,
                    }).then(function (response) {
                        _this.loading = false;
                        vueNorth.loading = false;

                        //console.log(response);
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.$message.success(responseData.message);

                                for(let node of responseData.flow.nodes) {
                                    _this.formData.nodes[node.index].item.output = node.item.output;
                                }

                                if (_this.currentNode.item.output !== false) {
                                    _this.formItems.current_node_output.codeMirror.setValue(JSON.stringify(_this.currentNode.item.output, null, 4));
                                }

                                //console.log(_this.formData);

                                _this.$forceUpdate();

                            } else {

                                // 更新节点间的输入和办理出
                                for(let i in _this.formData.nodes) {
                                    if (i >= _this.currentNode.index) {
                                        _this.formData.nodes[i].item.output = false;
                                    }
                                }

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
                },

                save: function (command) {
                    this.loading = true;
                    vueNorth.loading = true;

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
                                            //_this.formData.id = responseData.process.id;
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
                    if (this.dsTables[this.currentNode.item.ds_id] === undefined) {
                        var _this = this;
                        _this.$http.post("<?php echo beAdminUrl('Etl.Ds.getTableNames'); ?>", {
                            dsId: _this.currentNode.item.ds_id
                        }).then(function (response) {
                            if (response.status === 200) {
                                var responseData = response.data;
                                if (responseData.success) {
                                    _this.dsTables[_this.currentNode.item.ds_id] = responseData.data.tables;
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

                dsTableChange: function () {
                    if (this.dsTableFields[this.currentNode.item.ds_id] === undefined) {
                        this.dsTableFields[this.currentNode.item.ds_id] = {};
                    }

                    if (this.dsTableFields[this.currentNode.item.ds_id][this.currentNode.item.ds_table] === undefined) {
                        var _this = this;
                        this.$http.post("<?php echo beAdminUrl('Etl.Ds.getTableFields'); ?>", {
                            dsId: _this.currentNode.item.ds_id,
                            table: _this.currentNode.item.ds_table
                        }).then(function (response) {
                            if (response.status === 200) {
                                var responseData = response.data;
                                if (responseData.success) {

                                    _this.dsTableFields[_this.currentNode.item.ds_id][_this.currentNode.item.ds_table] = responseData.data.fields;

                                    if (_this.currentNode.type === 'input') {
                                        _this.inputDsTableFields = responseData.data.fields;
                                    } else if (_this.currentNode.type === 'output') {
                                        _this.outputDsTableFields = responseData.data.fields;
                                        _this.outputDsUpdateFieldMapping();
                                    }

                                } else {
                                    if (responseData.message) {
                                        _this.$message.error(responseData.message);
                                    }
                                }
                            }
                        }).catch(function (error) {
                            _this.$message.error(error);
                        });
                    } else {
                        if (this.currentNode.type === 'input') {
                            this.inputDsTableFields = this.dsTableFields[this.currentNode.item.ds_id][this.currentNode.item.ds_table];
                        } else if (this.currentNode.type === 'output') {
                            this.outputDsTableFields = this.dsTableFields[this.currentNode.item.ds_id][this.currentNode.item.ds_table];
                            this.outputDsUpdateFieldMapping();
                        }
                    }
                },

                dsSqlChange: function () {

                    if (this.dsTableFields[this.currentNode.item.ds_id] === undefined) {
                        this.dsTableFields[this.currentNode.item.ds_id] = {};
                    }

                    if (this.dsTableFields[this.currentNode.item.ds_id]['_sql'] === undefined) {
                        var _this = this;
                        this.$http.post("<?php echo beAdminUrl('Etl.Ds.getSqlFields'); ?>", {
                            dsId: _this.currentNode.item.ds_id,
                            sql: _this.currentNode.item.ds_sql
                        }).then(function (response) {
                            if (response.status === 200) {
                                var responseData = response.data;
                                if (responseData.success) {
                                    _this.dsTableFields[_this.currentNode.item.ds_id]['_sql'] = responseData.data.fields;

                                    // 仅输入 烽据源支持 SQL
                                    _this.inputDsTableFields = responseData.data.fields;
                                } else {
                                    if (responseData.message) {
                                        _this.$message.error(responseData.message);
                                    }
                                }
                            }
                        }).catch(function (error) {
                            _this.$message.error(error);
                        });
                    } else {
                        this.inputDsTableFields = this.dsTableFields[this.currentNode.item.ds_id]['_sql'];
                    }
                },


                processCleanCleanValuesInsertTag: function (tag) {
                    this.currentNode.item.clean_values += "\n{"  + tag +  "}";
                },


                processFilterFilterValuesInsertTag: function (tag) {
                    this.currentNode.item.filter_values += "\n{"  + tag +  "}";
                },


                processChatgptSystemPromptInsertTag: function (tag) {
                    this.currentNode.item.system_prompt += "{"  + tag +  "}";
                },
                processChatgptUserPromptInsertTag: function (tag) {
                    this.currentNode.item.user_prompt += "{"  + tag +  "}";
                },



                outputDsUpdateFieldMapping: function () {

                    if (this.outputDsTableFields.length === 0 ) return;

                    if (this.currentNode.item.field_mapping_details.length === 0 ) {
                        for (let i = 0; i < this.outputDsTableFields.length; i++) {
                            let outputField = this.outputDsTableFields[i].name;
                            if (this.currentNodeInput !== false) {

                                if (this.currentNodeInput.hasOwnProperty(outputField)) {
                                    this.currentNode.item.field_mapping_details.push({
                                        'enable' : 1,
                                        'field' : outputField,
                                        'type' : 'input_field',
                                        'input_field' : outputField,
                                        'custom' : "",
                                    });
                                } else {
                                    this.currentNode.item.field_mapping_details.push({
                                        'enable' : 1,
                                        'field' : outputField,
                                        'type' : 'custom',
                                        'input_field' : "",
                                        'custom' : "",
                                    });
                                }

                            } else {

                                this.currentNode.item.field_mapping_details.push({
                                    'enable' : 1,
                                    'field' : outputField,
                                    'type' : 'custom',
                                    'input_field' : "",
                                    'custom' : "",
                                });

                            }
                        }
                    }


                    // 生成 CODE
                    if (this.currentNode.item.field_mapping_code === "") {
                        let code = "$output = (object)[];\n";

                        for (let x of this.currentNode.item.field_mapping_details) {

                            if (x.enable !== 1) continue;

                            if (x.type === "input_field") {
                                code += "$output->" + x.field + " = $input->" + x.input_field + ";\n";
                            } else {
                                code += "$output->" + x.field + " = '" + x.custom + "';\n";
                            }
                        }

                        code += "return $output;";
                        this.currentNode.item.field_mapping_code = code;
                        this.formItems.output_ds_field_mapping_code.codeMirror.setValue(code);
                    }

                    this.$forceUpdate();
                },
                // 输出 数据源
                outputDsFieldMappingSelectAll: function () {
                    for (let x of this.currentNode.item.field_mapping_details) {
                        x.enable = 1;
                    }
                    this.$forceUpdate();
                },
                outputDsFieldMappingSelectNone: function () {
                    for (let x of this.currentNode.item.field_mapping_details) {
                        x.enable = 0;
                    }
                    this.$forceUpdate();
                },
                outputDsFieldMappingSelectMatched: function () {
                    for (let x of this.currentNode.item.field_mapping_details) {
                        if (this.currentNodeInput.hasOwnProperty(x.field)) {
                            x.enable = 1;
                        } else {
                            x.enable = 0;
                        }
                    }
                    this.$forceUpdate();
                },



                outputCsvFieldMappingAdd: function () {
                    this.currentNode.item.field_mapping_details.push({
                        field: "",
                        type: "custom",
                        input_field: "",
                        custom: "",
                    });
                    this.$forceUpdate();
                },
                outputCsvFieldMappingDelete: function (mapping) {
                    this.currentNode.item.field_mapping_details.splice(this.currentNode.item.field_mapping_details.indexOf(mapping), 1);
                    this.$forceUpdate();
                },
                outputCsvUpdateFieldMapping: function () {

                    if (this.currentNodeInput === false)  return;

                    if (this.currentNode.item.field_mapping_details.length === 0 ) {
                        for (let x in this.currentNodeInput) {
                            this.currentNode.item.field_mapping_details.push({
                                'field' : x,
                                'type' : 'input_field',
                                'input_field' : x,
                                'custom' : "",
                            });
                        }
                    }

                    // 生成 CODE
                    if (this.currentNode.item.field_mapping_code === "") {
                        let code = "$output = (object)[];\n";

                        for (let x of this.currentNode.item.field_mapping_details) {
                            if (x.type === "input_field") {
                                code += "$output->" + x.field + " = $input->" + x.input_field + ";\n";
                            } else {
                                code += "$output->" + x.field + " = '" + x.custom + "';\n";
                            }
                        }

                        code += "return $output;";
                        this.currentNode.item.field_mapping_code = code;
                        this.formItems.output_csv_field_mapping_code.codeMirror.setValue(code);
                    }

                    this.$forceUpdate();
                },



                outputFilesNameInsertTag: function (tag) {
                    this.currentNode.item.name_template += "{"  + tag +  "}";
                },
                outputFilesContentInsertTag: function (tag) {
                    this.currentNode.item.content_template += "{"  + tag +  "}";
                },


                outputFoldersNameInsertTag: function (tag) {
                    this.currentNode.item.name_template += "{"  + tag +  "}";
                },
                outputFoldersFileNameInsertTag: function (tag, fileIndex) {
                    this.currentNode.item.files[fileIndex].name_template += "{"  + tag +  "}";
                },
                outputFoldersFileContentInsertTag: function (tag, fileIndex) {
                    this.currentNode.item.files[fileIndex].content_template += "{"  + tag +  "}";
                },
                outputFoldersFilesAdd: function () {
                    this.currentNode.item.files.push({
                        name_template: "",
                        content_template: "",
                    });
                    this.$forceUpdate();
                },
                outputFoldersFilesDelete: function (file) {
                    this.currentNode.item.files.splice(this.currentNode.item.files.indexOf(file), 1);
                    this.$forceUpdate();
                },



                outputApiAddHeader: function () {
                    this.currentNode.item.headers.push({
                        name: "",
                        value: "",
                    });
                    this.$forceUpdate();
                },
                outputApiDeleteHeader: function (header) {
                    this.currentNode.item.headers.splice(this.currentNode.item.headers.indexOf(header), 1);
                    this.$forceUpdate();
                },
                outputApiFieldMappingAdd: function () {
                    this.currentNode.item.field_mapping_details.push({
                        field: "",
                        type: "custom",
                        input_field: "",
                        custom: "",
                    });
                    this.$forceUpdate();
                },
                outputApiFieldMappingDelete: function (mapping) {
                    this.currentNode.item.field_mapping_details.splice(this.currentNode.item.field_mapping_details.indexOf(mapping), 1);
                    this.$forceUpdate();
                },
                outputApiUpdateFieldMapping: function () {

                    if (this.currentNodeInput === false)  return;

                    if (this.currentNode.item.field_mapping_details.length === 0 ) {
                        for (let x in this.currentNodeInput) {
                            this.currentNode.item.field_mapping_details.push({
                                'field' : x,
                                'type' : 'input_field',
                                'input_field' : x,
                                'custom' : "",
                            });
                        }
                    }

                    // 生成 CODE
                    if (this.currentNode.item.field_mapping_code === "") {
                        let code = "$output = (object)[];\n";

                        for (let x of this.currentNode.item.field_mapping_details) {
                            if (x.type === "input_field") {
                                code += "$output->" + x.field + " = $input->" + x.input_field + ";\n";
                            } else {
                                code += "$output->" + x.field + " = '" + x.custom + "';\n";
                            }
                        }

                        code += "return $output;";
                        this.currentNode.item.field_mapping_code = code;
                        this.formItems.output_api_field_mapping_code.codeMirror.setValue(code);
                    }

                    this.$forceUpdate();
                },


                forceUpdate() {
                    this.$forceUpdate();
                }
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