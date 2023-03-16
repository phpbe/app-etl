<?php

namespace Be\App\Etl\Controller\Admin;

use Be\Be;

/**
 * @BeMenuGroup("控制台", icon="el-icon-monitor", ordering="9")
 * @BePermissionGroup("控制台", ordering="9")
 */
class Config
{

    /**
     * @BeMenu("参数", icon="el-icon-setting", ordering="9.1")
     * @BePermission("参数", ordering="9.1")
     */
    public function dashboard()
    {
        Be::getAdminPlugin('Config')->setting(['appName' => 'Etl'])->execute();
    }


}
