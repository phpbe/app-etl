<?php

namespace Be\App\Etl\Service;


class ExtractLog
{

    public function getStatusKeyValues() {
        return [
            '-1' => '出错',
            '0' => '创建',
            '1' => '运行中',
            '2' => '执行完成'
        ];
    }

    public function getTriggerKeyValues() {
        return [
            '0' => '系统调度',
            '1' => '人工启动',
        ];
    }


}
