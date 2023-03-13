<?php
namespace Be\Data\Runtime;

class AdminMenu extends \Be\AdminMenu\Driver
{
  public function __construct()
  {
    $this->addItem('System', '', 'el-icon-setting','系统', '', [], '', '');
    $this->addItem('System.Home.index', 'System', 'el-icon-bi bi-house-door', '首页', 'System.Home.index', [], '', '');
    $this->addItem('System.AdminUserLoginLog','System','el-icon-bi bi-person','管理员', 'System.AdminUser.adminUsers', [], '', '');
    $this->addItem('System.AdminUser.adminUsers', 'System.AdminUserLoginLog', 'el-icon-bi bi-people', '管理员管理', 'System.AdminUser.adminUsers', [], '', '');
    $this->addItem('System.AdminRole.roles', 'System.AdminUserLoginLog', 'el-icon-bi bi-person-video', '角色管理', 'System.AdminRole.roles', [], '', '');
    $this->addItem('System.AdminUserLoginLog.logs', 'System.AdminUserLoginLog', 'el-icon-bi bi-person-workspace', '登录日志', 'System.AdminUserLoginLog.logs', [], '', '');
    $this->addItem('System.Theme','System','el-icon-magic-stick','网站装修', 'System.Menu.menus', [], '', '');
    $this->addItem('System.Menu.menus', 'System.Theme', 'el-icon-position', '菜单导航', 'System.Menu.menus', [], '', '');
    $this->addItem('System.Theme.themes', 'System.Theme', 'el-icon-view', '前台主题', 'System.Theme.themes', [], '', '');
    $this->addItem('System.AdminTheme.themes', 'System.Theme', 'el-icon-view', '后台主题', 'System.AdminTheme.themes', [], '', '');
    $this->addItem('System.Task','System','el-icon-monitor','控制台', 'System.App.apps', [], '', '');
    $this->addItem('System.App.apps', 'System.Task', 'el-icon-files', '应用', 'System.App.apps', [], '', '');
    $this->addItem('System.Config.dashboard', 'System.Task', 'el-icon-setting', '参数', 'System.Config.dashboard', [], '', '');
    $this->addItem('System.Task.dashboard', 'System.Task', 'el-icon-timer', '计划任务', 'System.Task.dashboard', [], '', '');
    $this->addItem('System.Storage.index', 'System.Task', 'el-icon-folder', '存储', 'System.Storage.index', [], '', '');
    $this->addItem('System.Server.stats', 'System.Task', 'el-icon-info', '服务器状态', 'System.Server.stats', [], '', '');
    $this->addItem('System.RuntimeCache.index', 'System.Task', 'el-icon-bi bi-hdd', '运行时缓存', 'System.RuntimeCache.index', [], '', '');
    $this->addItem('System.Log','System','el-icon-bi bi-file-earmark-text','日志', 'System.AdminOpLog.logs', [], '', '');
    $this->addItem('System.AdminOpLog.logs', 'System.Log', 'el-icon-bi bi-file-earmark-check', '后台操作日志', 'System.AdminOpLog.logs', [], '', '');
    $this->addItem('System.Log.lists', 'System.Log', 'el-icon-bi bi-file-earmark-excel', '系统日志', 'System.Log.lists', [], '', '');
    $this->addItem('Etl', '', 'el-icon-bi bi-box-arrow-right','ETL', '', [], '', '');
    $this->addItem('Etl.Ds','Etl','el-icon-fa fa-database','数据源', 'Etl.Ds.lists', [], '', '');
    $this->addItem('Etl.Ds.lists', 'Etl.Ds', 'el-icon-fa fa-list-ul', '数据源管理', 'Etl.Ds.lists', [], '', '');
    $this->addItem('Etl.ExtractCategory','Etl','el-icon-fa fa-copy','抽取', 'Etl.Extract.lists', [], '', '');
    $this->addItem('Etl.Extract.lists', 'Etl.ExtractCategory', 'el-icon-fa fa-list-ul', '任务管理', 'Etl.Extract.lists', [], '', '');
    $this->addItem('Etl.ExtractCategory.lists', 'Etl.ExtractCategory', 'el-icon-fa fa-bookmark', '分类管理', 'Etl.ExtractCategory.lists', [], '', '');
    $this->addItem('Etl.Config','Etl','el-icon-folder','配置', 'Etl.Config.dashboard', [], '', '');
    $this->addItem('Etl.Config.dashboard', 'Etl.Config', 'el-icon-setting', '配置', 'Etl.Config.dashboard', [], '', '');
  }
}
