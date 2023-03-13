<?php

namespace Be\App\Etl\Service\Admin;


class ExtractLog
{

    public function getStatusKeyValues() {
        return [
            'error' => '出错',
            'create' => '创建',
            'running' => '运行中',
            'finish' => '执行完成'
        ];
    }

    public function getTriggerKeyValues() {
        return [
            'system' => '系统调度',
            'manual' => '人工启动',
        ];
    }


}
