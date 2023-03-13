<?php
namespace Be\Data\Runtime\Tuple\master;

class system_admin_op_log extends \Be\Db\Tuple
{
    protected string $_dbName = 'master'; // 数据库名
    protected string $_tableName = 'system_admin_op_log'; // 表名
    protected $_primaryKey = 'id'; // 主键
    protected $id = 'uuid()'; // UUID
    protected $admin_user_id = ''; // 用户ID
    protected $app = ''; // 应用名
    protected $controller = ''; // 控制器名
    protected $action = ''; // 动作名
    protected $content = ''; // 内容
    protected $details = null; // 明细
    protected $ip = ''; // IP
    protected $create_time = 'CURRENT_TIMESTAMP'; // 创建时间
}

