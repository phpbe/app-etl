
<be-page-content>
    <div class="be-p-150 be-bc-fff">
        <div>
            运行日志ID：<?php echo $this->flowLog->id; ?>
        </div>

        <div class="be-mt-100">
            数据流：<?php echo $this->flowLog->flow->name; ?>（#<?php echo $this->flowLog->flow->id; ?>）
        </div>

        <div class="be-mt-100">
            状态：<?php echo $this->flowLog->status; ?>
        </div>

        <?php if ($this->flowLog->message !== '') { ?>
        <div class="be-mt-100">
            消息：<?php echo $this->flowLog->message; ?>
        </div>
        <?php } ?>

        <div class="be-row be-mt-100">
            <div class="be-col-auto">
                总数据量：
            </div>
            <div class="be-col">
                <?php echo $this->flowLog->total; ?>
            </div>
            <div class="be-col-auto">
                总成功数据数：
            </div>
            <div class="be-col">
                <?php echo $this->flowLog->total_success; ?>
            </div>
        </div>


        <?php if ($this->flowLog->finish_time !== '1970-01-02 00:00:00') { ?>
            <div class="be-mt-100">
                完成时间：<?php echo $this->flowLog->finish_time; ?>
            </div>
        <?php } ?>

        <div class="be-mt-100">
            创建时间：<?php echo $this->flowLog->create_time; ?>
        </div>

        <div class="be-mt-100">
            更新时间：<?php echo $this->flowLog->update_time; ?>
        </div>

    </div>


    <div class="be-mt-150 be-p-150 be-bc-fff">
        <div class="be-fs-110">处理节点</div>

        <?php
        foreach($this->flowLog->nodeLogs as $nodeLog) {
            ?>
            <div class="be-row be-mt-100 be-mb-300">
                <div class="be-col-auto" style="width: 200px">
                    <?php
                    echo $nodeLog->index + 1;
                    echo '&nbsp;';
                    echo $nodeLog->node->itemTypeName;
                    echo '&nbsp;';
                    echo $nodeLog->node->itemName;
                    ?>：
                </div>
                <div class="be-col-auto">
                    <div class="be-pl-100 be-pt-100"></div>
                </div>
                <div class="be-col">
                    <div>
                        总成功数据数：<?php echo $nodeLog->total_success; ?>
                    </div>

                    <?php if ($nodeLog->output_file !== '') { ?>
                    <div class="be-mt-100">
                        <a class="be-link-major" href="<?php echo beAdminUrl('Etl.FlowLog.downloadOutputFile', ['flow_node_log_id' => $nodeLog->id]); ?>" target="_blank">下载输出特</a>
                    </div>
                    <?php } ?>

                    <div class="be-mt-100">
                        <a class="be-link-major" href="javascript:void ();" onclick="$(this).next().toggle()">参数快照</a>
                        <div style="display: none;">
                            <pre><?php echo json_encode($nodeLog->config, JSON_PRETTY_PRINT); ?></pre>
                        </div>
                    </div>

                    <div class="be-mt-100">
                        <a class="be-link-major" href="<?php echo beAdminUrl('Etl.FlowLog.nodeItemLogs', ['flow_node_log_id' => $nodeLog->id]); ?>" target="_blank">详细记录</a>
                    </div>

                    <div class="be-mt-100">
                        创建时间： <?php echo $nodeLog->create_time; ?>
                    </div>

                    <div class="be-mt-100">
                        更新时间： <?php echo $nodeLog->update_time; ?>
                    </div>

                </div>
            </div>
            <?php
        }
        ?>
    </div>
</be-page-content>