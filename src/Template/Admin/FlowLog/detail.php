
<be-page-content>
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
        <div class="be-col-auto be-lh-200">
            总数据量：
        </div>
        <div class="be-col-auto">
            <span class="be-fs-200 be-c-major  be-lh-200 be-pr-200"><?php echo $this->flowLog->total; ?></span>
        </div>
        <div class="be-col-auto be-lh-200">
            已处理：
        </div>
        <div class="be-col be-lh-200">
            <span class="be-fs-200 be-c-green  be-lh-200"><?php echo $this->flowLog->total_success; ?></span>
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

    <div class="be-mt-200 be-fs-110 be-fw-bold be-pb-50 be-bb-ddd">处理节点</div>
    <?php
    foreach($this->flowLog->nodeLogs as $nodeLog) {
        ?>
        <div class="be-row be-mt-100 be-pb-200 be-bb-eee be-mb-200">
            <div class="be-col-auto" style="width: 200px">
                <?php
                echo '<span class="be-d-inline-block be-mt-50 be-c-fff be-px-100 be-py-50 ';
                switch ($nodeLog->node->type) {
                    case 'input':
                        echo 'be-bc-major';
                        break;
                    case 'process':
                        echo 'be-bc-orange';
                        break;
                    case 'output':
                        echo 'be-bc-green';
                        break;
                }
                echo '">';

                echo $nodeLog->index + 1;
                echo '&nbsp;';
                echo $nodeLog->node->itemTypeName;
                echo '&nbsp;';
                echo $nodeLog->node->itemName;

                echo '</span>';
                ?>
            </div>
            <div class="be-col-auto">
                <div class="be-pl-100 be-pt-100"></div>
            </div>
            <div class="be-col" style="min-width: 0;">

                <div class="be-row">
                    <div class="be-col-auto be-lh-200">
                        总处理数据数：
                    </div>
                    <div class="be-col">
                        <span class="be-fs-200 be-c-green be-lh-200"><?php echo $nodeLog->total_success; ?></span>
                    </div>
                </div>

                <?php if ($nodeLog->output_file !== '') { ?>
                <div class="be-mt-100">
                    <a class="be-link-major" href="<?php echo beAdminUrl('Etl.FlowLog.downloadOutputFile', ['flow_node_log_id' => $nodeLog->id]); ?>" target="_blank">下载输出特</a>
                </div>
                <?php } ?>

                <div class="be-mt-100">
                    <a class="be-link-major" href="javascript:void ();" onclick="$(this).next().toggle()">参数快照</a>
                    <div style="display: none;">
                        <pre style="white-space: pre-wrap; word-wrap: break-word; "><?php echo json_encode($nodeLog->config, JSON_PRETTY_PRINT); ?></pre>
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
</be-page-content>