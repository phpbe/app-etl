<?php
namespace Be\Data\Runtime\Tuple\master;

class etl_extract extends \Be\Db\Tuple
{
    protected string $_dbName = 'master'; // 数据库名
    protected string $_tableName = 'etl_extract'; // 表名
    protected $_primaryKey = 'id'; // 主键
    protected $id = null; // 自增ID
    protected $name = ''; // 名称
    protected $category_id = 0; // 分类ID
    protected $src_ds_id = 0; // 来源数据源ID
    protected $dst_ds_id = 0; // 目标数据源ID
    protected $src_type = 0; // 来源数据源类型（0 - 表 / 1：SQL
    protected $src_table = ''; // 来源数据源表名
    protected $src_sql = ''; // 来源数据源SQL
    protected $dst_table = ''; // 目标数据源表名
    protected $field_mapping_type = 0; // 字段映射类型（0:不需要映射/1：字段映射/2：代码处理）
    protected $field_mapping = null; // 字段映射
    protected $field_mapping_code = null; // 代码映射
    protected $breakpoint_type = 0; // 断点类型（0：全量/1：有断点）
    protected $breakpoint_field = ''; // 断点字段
    protected $breakpoint = '1970-01-02 00:00:00'; // 断点
    protected $breakpoint_step = ''; // 断点递增量(1_HOUR:一小时/1_DAY:一天/1_MONTH:一个月)
    protected $breakpoint_offset = 0; // 断点向前偏移量（秒）
    protected $schedule = ''; // 执行计划
    protected $is_enable = 1; // 是否可用
    protected $is_delete = 0; // 是否已删除
    protected $create_time = 'CURRENT_TIMESTAMP'; // 创建时间
    protected $update_time = 'CURRENT_TIMESTAMP'; // 更新时间
}

