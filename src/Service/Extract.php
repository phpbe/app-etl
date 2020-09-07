<?php

namespace Be\App\Etl\Service;


use Be\System\Be;

class Extract extends \Be\System\Service
{

    public function getIdNameKeyValues()
    {
        return Be::getTable('etl_extract')
            ->where('is_delete', 0)
            ->where('is_enable', 1)
            ->getKeyValues('id', 'name');
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
