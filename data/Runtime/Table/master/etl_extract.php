<?php
namespace Be\Data\Runtime\Table\master;

class etl_extract extends \Be\Db\Table
{
    protected string $_dbName = 'master'; // 数据库名
    protected string $_tableName = 'etl_extract'; // 表名
    protected $_primaryKey = 'id'; // 主键
    protected array $_fields = ['id','name','category_id','src_ds_id','dst_ds_id','src_type','src_table','src_sql','dst_table','field_mapping_type','field_mapping','field_mapping_code','breakpoint_type','breakpoint_field','breakpoint','breakpoint_step','breakpoint_offset','schedule','is_enable','is_delete','create_time','update_time']; // 字段列表
}

