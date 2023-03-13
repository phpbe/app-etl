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
            'table' => '表',
            'sql' => 'SQL语句',
        ];
    }

    public function getFieldMappingTypeKeyValues()
    {
        return [
            'same' => '完全一致',
            'mapping' => '字段映射',
            'code' => '代码处理',
        ];
    }

    public function getBreakpointTypeKeyValues()
    {
        return [
            'full' => '全量',
            'breakpoint' => '有断点',
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
