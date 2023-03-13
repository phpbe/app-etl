<?php
namespace Be\Data\Runtime\TableProperty\master;

class system_admin_user_login_log extends \Be\Db\TableProperty
{
    protected string $_dbName = 'master'; // 数据库名
    protected string $_tableName = 'system_admin_user_login_log'; // 表名
    protected $_primaryKey = 'id'; // 主键
    protected array $_fields = array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'varchar',
    'length' => '36',
    'precision' => '',
    'scale' => '',
    'comment' => 'UUID',
    'default' => 'uuid()',
    'nullAble' => false,
    'unsigned' => false,
    'collation' => 'utf8mb4_general_ci',
    'key' => 'PRI',
    'extra' => '',
    'privileges' => 'select,insert,update,references',
    'autoIncrement' => 0,
    'isNumber' => 0,
  ),
  'username' => 
  array (
    'name' => 'username',
    'type' => 'varchar',
    'length' => '120',
    'precision' => '',
    'scale' => '',
    'comment' => '用户名',
    'default' => '',
    'nullAble' => false,
    'unsigned' => false,
    'collation' => 'utf8mb4_general_ci',
    'key' => '',
    'extra' => '',
    'privileges' => 'select,insert,update,references',
    'autoIncrement' => 0,
    'isNumber' => 0,
  ),
  'success' => 
  array (
    'name' => 'success',
    'type' => 'tinyint',
    'length' => '4',
    'precision' => '',
    'scale' => '',
    'comment' => '是否登录成功（0-不成功/1-成功）',
    'default' => '0',
    'nullAble' => false,
    'unsigned' => false,
    'collation' => NULL,
    'key' => '',
    'extra' => '',
    'privileges' => 'select,insert,update,references',
    'autoIncrement' => 0,
    'isNumber' => 1,
  ),
  'description' => 
  array (
    'name' => 'description',
    'type' => 'varchar',
    'length' => '240',
    'precision' => '',
    'scale' => '',
    'comment' => '描述',
    'default' => '',
    'nullAble' => false,
    'unsigned' => false,
    'collation' => 'utf8mb4_general_ci',
    'key' => '',
    'extra' => '',
    'privileges' => 'select,insert,update,references',
    'autoIncrement' => 0,
    'isNumber' => 0,
  ),
  'ip' => 
  array (
    'name' => 'ip',
    'type' => 'varchar',
    'length' => '15',
    'precision' => '',
    'scale' => '',
    'comment' => 'IP',
    'default' => '',
    'nullAble' => false,
    'unsigned' => false,
    'collation' => 'utf8mb4_general_ci',
    'key' => '',
    'extra' => '',
    'privileges' => 'select,insert,update,references',
    'autoIncrement' => 0,
    'isNumber' => 0,
  ),
  'create_time' => 
  array (
    'name' => 'create_time',
    'type' => 'timestamp',
    'length' => '',
    'precision' => '',
    'scale' => '',
    'comment' => '创建时间',
    'default' => 'CURRENT_TIMESTAMP',
    'nullAble' => false,
    'unsigned' => false,
    'collation' => NULL,
    'key' => '',
    'extra' => '',
    'privileges' => 'select,insert,update,references',
    'autoIncrement' => 0,
    'isNumber' => 0,
  ),
); // 字段列表
}

