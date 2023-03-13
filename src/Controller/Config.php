<?php

namespace Be\App\Etl\Controller;

use Be\Be;

/**
 * @BeMenuGroup("配置", ordering="40")
 * @BePermissionGroup("配置", ordering="40")
 */
class Config
{

    /**
     * @BeMenu("配置", icon="el-icon-setting", ordering="40")
     * @BePermission("配置", ordering="40")
     */
    public function dashboard()
    {
        Be::getPlugin('Config')->setting(['appName' => 'Etl'])->execute();
    }


}
