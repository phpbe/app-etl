<?php
namespace Be\Data\Runtime\Tuple\master;

class system_admin_user_login_log extends \Be\Db\Tuple
{
    protected string $_dbName = 'master'; // 数据库名
    protected string $_tableName = 'system_admin_user_login_log'; // 表名
    protected $_primaryKey = 'id'; // 主键
    protected $id = 'uuid()'; // UUID
    protected $username = ''; // 用户名
    protected $success = 0; // 是否登录成功（0-不成功/1-成功）
    protected $description = ''; // 描述
    protected $ip = ''; // IP
    protected $create_time = 'CURRENT_TIMESTAMP'; // 创建时间
}

