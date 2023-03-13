<?php
namespace Be\App\Etl\Config;

/**
 * @BeConfig("抽取设置")
 */
class Extract
{
    /**
     * @BeConfigItem("全量同步时，数据删除方式",
     *     driver="FormItemSelect",
     *     keyValues = "return ['truncate' => 'TRUNCATE', 'delete' => 'DELETE'];")
     */
    public string $deleteType = 'truncate';

    /**
     * @BeConfigItem("批量处理的数据量", driver="FormItemInputNumberInt")
     */
    public int $batchQuantity = 5000;

    /**
     * @BeConfigItem("计划任务无更新超时时间(秒)", driver="FormItemInputNumberInt")
     */
    public int $timeout = 3600;

    /**
     * @BeConfigItem("MYSQL 数据库是否启用 Replace Into 插入",
     *     driver="FormItemSwitch",
     *     description="启用时，写入MYSQL数据时，将优先使用Replace批量插入，以提升效率。")
     */
    public int $mysqlUseReplaceFirst = 0;

}
