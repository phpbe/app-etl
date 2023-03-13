<?php

namespace Be\App\Etl\Service\Admin;


use Be\Be;

class Extract
{

    public function getIdNameKeyValues()
    {
        return Be::getTable('etl_extract')
            ->where('is_delete', 0)
            ->where('is_enable', 1)
            ->getKeyValues('id', 'name');
    }


    public function getSrcTypeKeyValues()
    {
        return [
            '0' => '表',
            '1' => 'SQL语句',
        ];
    }

    public function getFieldMappingTypeKeyValues()
    {
        return [
            '0' => '完全一致',
            '1' => '字段映射',
            '2' => '代码处理',
        ];
    }

    public function getBreakpointTypeKeyValues()
    {
        return [
            '0' => '全量',
            '1' => '有断点',
        ];
    }

    public function getBreakpointStepKeyValues()
    {
        return [
            '1_HOUR' => '一小时',
            '1_DAY' => '一天',
            '1_MONTH' => '一个月',
        ];
    }

}
