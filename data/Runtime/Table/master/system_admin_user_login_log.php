<?php
namespace Be\Data\Runtime\Table\master;

class system_admin_user_login_log extends \Be\Db\Table
{
    protected string $_dbName = 'master'; // 数据库名
    protected string $_tableName = 'system_admin_user_login_log'; // 表名
    protected $_primaryKey = 'id'; // 主键
    protected array $_fields = ['id','username','success','description','ip','create_time']; // 字段列表
}

