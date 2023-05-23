<be-page-content>
    <div id="app" v-cloak>
        <div class="be-p-150 be-bc-fff">
            <div class="be-row be-lh-250 be-bb be-pb-50">
                <div class="be-col-auto">选择素材：</div>
                <div class="be-col-auto be-px-100">
                    <?php
                    foreach ($this->materialIdNameKeyValues as $id => $label) {
                        ?>
                        <div class="be-mb-100">
                            <el-link type="primary" href="<?php echo beAdminUrl('Etl.MaterialItem.index', ['material_id' => $id]); ?>"><?php echo $label; ?></el-link>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script>new Vue({el: '#app'});</script>
</be-page-content>