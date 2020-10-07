<?php
namespace Be\App\Etl\Config;

/**
 * @BeConfig("抽取设置")
 */
class Extract
{
    /**
     * @BeConfigItem("全量同步时，数据清理方式",
     *     driver="FormItemSelect",
     *     keyValues = "return ['truncate' => 'TRUNCATE', 'delete' => 'DELETE'];")
     */
    public $clearType = 'truncate';

    /**
     * @BeConfigItem("批量处理的数据量", driver="FormItemInputNumberInt")
     */
    public $batchQuantity = 1000;

    /**
     * @BeConfigItem("计划任务无更新超时时间(秒)", driver="FormItemInputNumberInt")
     */
    public $timeout = 3600;

}
