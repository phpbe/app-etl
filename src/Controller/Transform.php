<?php

namespace Be\App\Etl\Controller;

use Be\System\Controller;

/**
 * Class Ds
 * @package App\Etl\Controller
 *
 * @BeMenuGroup("加工", icon="el-icon-fa fa-exchange", ordering="30")
 * @BePermissionGroup("加工")
 */
class Transform extends Controller
{
    /**
     * 任务管理
     *
     * @BeMenu("任务管理", icon="el-icon-fa fa-list-ul", ordering="30")
     * @BePermission("任务管理")
     */
    public function lists()
    {

    }


}
