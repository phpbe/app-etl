<?php
namespace Be\Data\Runtime\Table\master;

class etl_extract_category extends \Be\Db\Table
{
    protected string $_dbName = 'master'; // 数据库名
    protected string $_tableName = 'etl_extract_category'; // 表名
    protected $_primaryKey = 'id'; // 主键
    protected array $_fields = ['id','name','ordering','is_enable','is_delete','create_time','update_time']; // 字段列表
}

