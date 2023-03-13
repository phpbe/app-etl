<?php
namespace Be\Data\Runtime\Table\master;

class system_admin_user extends \Be\Db\Table
{
    protected string $_dbName = 'master'; // 数据库名
    protected string $_tableName = 'system_admin_user'; // 表名
    protected $_primaryKey = 'id'; // 主键
    protected array $_fields = ['id','username','password','salt','admin_role_id','avatar','email','name','gender','phone','mobile','last_login_time','this_login_time','last_login_ip','this_login_ip','is_enable','is_delete','create_time','update_time']; // 字段列表
}

