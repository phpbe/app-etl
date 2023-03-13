<?php
namespace Be\Data\Runtime\Tuple\master;

class system_admin_role extends \Be\Db\Tuple
{
    protected string $_dbName = 'master'; // 数据库名
    protected string $_tableName = 'system_admin_role'; // 表名
    protected $_primaryKey = 'id'; // 主键
    protected $id = 'uuid()'; // UUID
    protected $name = ''; // 角色名
    protected $remark = ''; // 备注
    protected $permission = 0; // 权限（0: 无权限/1: 所有权限/-1: 自定义权限）
    protected $permission_keys = null; // 自定义权限
    protected $is_enable = 1; // 是否可用
    protected $is_delete = 0; // 是否已删除
    protected $ordering = 0; // 排序（越小越靠前）
    protected $create_time = 'CURRENT_TIMESTAMP'; // 创建时间
    protected $update_time = 'CURRENT_TIMESTAMP'; // 更新时间
}

