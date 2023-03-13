<?php
namespace Be\Data\Runtime\Tuple\master;

class system_admin_user extends \Be\Db\Tuple
{
    protected string $_dbName = 'master'; // 数据库名
    protected string $_tableName = 'system_admin_user'; // 表名
    protected $_primaryKey = 'id'; // 主键
    protected $id = 'uuid()'; // UUID
    protected $username = ''; // 用户名
    protected $password = ''; // 密码
    protected $salt = ''; // 密码盐值
    protected $admin_role_id = ''; // 角色ID
    protected $avatar = ''; // 头像
    protected $email = ''; // 邮箱
    protected $name = ''; // 名称
    protected $gender = -1; // 性别（0：女/1：男/-1：保密）
    protected $phone = ''; // 电话
    protected $mobile = ''; // 手机
    protected $last_login_time = null; // 上次登陆时间
    protected $this_login_time = null; // 本次登陆时间
    protected $last_login_ip = ''; // 上次登录的IP
    protected $this_login_ip = ''; // 本次登录的IP
    protected $is_enable = 1; // 是否可用
    protected $is_delete = 0; // 是否已删除
    protected $create_time = 'CURRENT_TIMESTAMP'; // 创建时间
    protected $update_time = 'CURRENT_TIMESTAMP'; // 更新时间
}

