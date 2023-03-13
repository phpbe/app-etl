<?php
namespace Be\Data\Runtime\Table\master;

class etl_ds extends \Be\Db\Table
{
    protected string $_dbName = 'master'; // 数据库名
    protected string $_tableName = 'etl_ds'; // 表名
    protected $_primaryKey = 'id'; // 主键
    protected array $_fields = ['id','name','type','db_host','db_port','db_user','db_pass','db_name','db_charset','remark','is_enable','is_delete','create_time','update_time']; // 字段列表
}

