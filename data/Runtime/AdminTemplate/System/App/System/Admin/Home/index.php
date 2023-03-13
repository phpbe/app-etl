<?php
namespace Be\Data\Runtime\AdminTemplate\System\App\System\Admin\Home;


class index extends \Be\Template\Driver
{
  public array $_tags = array (
  'be-body' => 
  array (
    0 => '<div class="be-body">',
    1 => '</div>',
  ),
  'be-north' => 
  array (
    0 => '<div class="be-north" id="be-north">',
    1 => '</div>',
  ),
  'be-middle' => 
  array (
    0 => '<div class="be-middle" id="be-middle">',
    1 => '</div>',
  ),
  'be-west' => 
  array (
    0 => '<div class="be-west" id="be-west">',
    1 => '</div>',
  ),
  'be-center' => 
  array (
    0 => '<div class="be-px-200">',
    1 => '</div>',
  ),
  'be-east' => 
  array (
    0 => '<div class="be-east">',
    1 => '</div>',
  ),
  'be-south' => 
  array (
    0 => '<div class="be-south">',
    1 => '</div>',
  ),
  'be-page-title' => 
  array (
    0 => '<div class="be-page-title">',
    1 => '</div>',
  ),
  'be-page-content' => 
  array (
    0 => '<div class="be-page-content">',
    1 => '</div>',
  ),
  'be-section' => 
  array (
    0 => '<div class="be-section">',
    1 => '</div>',
  ),
  'be-section-title' => 
  array (
    0 => '<div class="be-section-title">',
    1 => '</div>',
  ),
  'be-section-content' => 
  array (
    0 => '<div class="be-section-content">',
    1 => '</div>',
  ),
);
  public function html()
  {
    ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $this->title; ?></title>

    <?php
    $beUrl = beUrl();
    $appSystemWwwUrl = \Be\Be::getProperty('App.System')->getWwwUrl();
    $adminThemeWwwUrl = \Be\Be::getProperty('AdminTheme.System')->getWwwUrl();
    ?>
    <base href="<?php echo $beUrl; ?>/" >
    <script>var beUrl = "<?php echo $beUrl; ?>"; </script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/jquery/jquery-1.12.4.min.js"></script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/vue/vue-2.6.11.min.js"></script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/axios/axios-0.19.0.min.js"></script>
    <script>Vue.prototype.$http = axios;</script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/vue-cookies/vue-cookies-1.5.13.js"></script>

    <link rel="stylesheet" href="<?php echo $appSystemWwwUrl; ?>/lib/element-ui/element-ui-2.15.7.css">
    <script src="<?php echo $appSystemWwwUrl; ?>/lib/element-ui/element-ui-2.15.7.js"></script>

    <link rel="stylesheet" href="//cdn.phpbe.com/ui/be.css?v=20220926" />
    <link rel="stylesheet" href="//cdn.phpbe.com/ui/be-icons.css"/>

    <link rel="stylesheet" href="<?php echo $adminThemeWwwUrl; ?>/css/theme.css?v=20220925" />

    <?php $this->head(); ?>
</head>
<body>
    <?php $this->body(); ?>

    <div id="app-be" v-cloak>
        <el-dialog
                class="be-dialog"
                :title="dialog.title"
                :visible.sync="dialog.visible"
                :width="dialog.width"
                :close-on-click-modal="false"
                :destroy-on-close="true">
            <iframe id="frame-be-dialog" name="frame-be-dialog" :src="dialog.url" :style="{width:'100%',height:dialog.height,border:0}"></iframe>
        </el-dialog>

        <el-drawer
                class="be-drawer"
                :visible.sync="drawer.visible"
                :size="drawer.width"
                :title="drawer.title"
                :wrapper-closable="false"
                :destroy-on-close="true">
            <div style="padding:0 10px;height: 100%;overflow:hidden;">
                <iframe id="frame-be-drawer" name="frame-be-drawer" :src="drawer.url" style="width:100%;height:100%;border:0;"></iframe>
            </div>
        </el-drawer>
    </div>
    <script src="<?php echo $adminThemeWwwUrl; ?>/js/theme.js?v=20220716"></script>

</body>
</html>
    <?php
  }

public function center()
{
  if ($this->pageConfig->center !== 0) {
    echo $this->tag0('be-center');
$my = \Be\Be::getAdminUser();
$configAdminUser = \Be\Be::getConfig('App.System.AdminUser');
?>
<div id="app" style="padding: 30px 0" v-cloak>

    <el-row :gutter="20">
        <el-col :span="12">

            <el-card shadow="hover" style="height: 180px;">
                <el-image src="<?php
                if ($this->adminUser->avatar === '') {
                    echo \Be\Be::getProperty('App.System')->getWwwUrl().'/admin/admin-user/images/avatar.png';
                } else {
                    echo \Be\Be::getStorage()->getRootUrl() . '/app/system/admin-user/avatar/'. $this->adminUser->avatar;
                }
                ?>"></el-image>

                <div class="be-fw-bold"><?php echo $this->adminUser->name; ?>（<?php echo $my->getRoleName(); ?>）</div>
                <div class="be-c-999">上次登陆时间：<?php echo $this->adminUser->last_login_time; ?></div>
            </el-card>

        </el-col>

        <el-col :span="3">
            <el-card shadow="hover" style="height: 180px; text-align:center;">
                <div slot="header" class="clearfix">
                    <span>应用数</span>
                </div>

                <el-link href="<?php echo beAdminUrl('System.App.apps'); ?>" style="font-size:36px; ">
                    <?php echo $this->appCount; ?>
                </el-link>
            </el-card>
        </el-col>


        <el-col :span="3">
            <el-card shadow="hover" style="height: 180px; text-align:center;">
                <div slot="header" class="clearfix">
                    <span>前台主题数</span>
                </div>

                <el-link href="<?php echo beAdminUrl('System.Theme.themes'); ?>" style="font-size:36px; ">
                    <?php echo $this->themeCount;; ?>
                </el-link>
            </el-card>
        </el-col>

        <el-col :span="3">
            <el-card shadow="hover" style="height: 180px; text-align:center;">
                <div slot="header" class="clearfix">
                    <span>后台主题数</span>
                </div>

                <el-link href="<?php echo beAdminUrl('System.AdminTheme.themes'); ?>" style="font-size:36px; ">
                    <?php echo $this->adminThemeCount;; ?>
                </el-link>
            </el-card>
        </el-col>

        <el-col :span="3">
            <el-card shadow="hover" style="height: 180px; text-align:center;">
                <div slot="header" class="clearfix">
                    <span>管理员</span>
                </div>

                <el-link href="<?php echo beAdminUrl('System.AdminUser.users'); ?>" style="font-size:36px; ">
                    <?php echo $this->adminUserCount; ?>
                </el-link>
            </el-card>
        </el-col>

    </el-row>




    <el-row :gutter="20" style="margin-top: 20px;">
        <el-col :span="12">

            <el-card shadow="hover">
                <div slot="header" class="clearfix">
                    <span>最近操作日志</span>
                    <el-button style="float: right; padding: 3px 0" type="text" @click="window.location.href='<?php echo beAdminUrl('System.AdminOpLog.logs')?>'">更多..</el-button>
                </div>

                <el-table :data="recentLogs" stripe size="medium">
                    <el-table-column
                            prop="create_time"
                            label="时间"
                            width="180"
                            align="center">
                        <template slot-scope="scope">
                            <div v-html="scope.row.create_time"></div>
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="content"
                            label="操作">
                    </el-table-column>
                </el-table>

            </el-card>

        </el-col>

        <el-col :span="12">

            <el-card shadow="hover">
                <div slot="header" class="clearfix">
                    <span>最近登录日志</span>
                    <el-button style="float: right; padding: 3px 0" type="text" @click="window.location.href='<?php echo beAdminUrl('System.AdminUserLoginLog.logs')?>'">更多..</el-button>
                </div>

                <el-table :data="recentLoginLogs" stripe size="medium">
                    <el-table-column
                            prop="create_time"
                            label="时间"
                            width="180"
                            align="center">
                        <template slot-scope="scope">
                            <div v-html="scope.row.create_time"></div>
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="description"
                            label="操作">
                    </el-table-column>
                </el-table>

            </el-card>

        </el-col>
    </el-row>


</div>

<?php
foreach ($this->recentLogs as $log) {
    $log->create_time = date('Y-m-d H:i', strtotime($log->create_time));
}

foreach ($this->recentLoginLogs as $log) {
    $log->create_time = date('Y-m-d H:i', strtotime($log->create_time));
}
?>
<script>
    var vue = new Vue({
        el: '#app',
        data: {
            recentLogs : <?php echo json_encode($this->recentLogs); ?>,
            recentLoginLogs : <?php echo json_encode($this->recentLoginLogs); ?>
        },
        methods: {
        }
    });
</script>
    <?php
    echo $this->tag1('be-center');
  }
}

public function west()
{
  if ($this->pageConfig->west !== 0) {
    echo $this->tag0('be-west');
    ?>
<div id="app-west" :class="{'be-west-collapse': collapse}" v-cloak>
                <?php
                /*
                <div class="logo">
                    <a href="<?php echo beAdminUrl(); ?>"></a>
                </div>
                 */
                ?>

                <div class="logo">
                    <a href="<?php echo beAdminUrl(); ?>">
                        <?php
                        $configTheme = \Be\Be::getConfig('AdminTheme.System.Theme');
                        if ($configTheme->logo !== '') {
                            echo '<img src="' . $configTheme->logo . '">';
                        } else {
                            ?>
                            <svg viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg">
                                <rect rx="5" height="40" width="40" x="10" y="10" fill="#ff5c35"/>
                                <path d="M16 29 L21 29 M21 42 L16 42 L16 17 L21 17 C30 17 30 29 21 29 C30 30 30 42 21 42 M45 17 L34 17 L34 42 L46 42 M35 29 L44 29" stroke="#ffffff" stroke-width="2" fill="none" />
                                <text x="65" y="28" style="font-size: 14px;"><tspan fill="#ff5c35">B</tspan><tspan fill="#999999">eyound</tspan></text>
                                <text x="90" y="42" style="font-size: 14px;"><tspan fill="#ff5c35">E</tspan><tspan fill="#999999">xception</tspan></text>
                            </svg>
                            <?php
                        }
                        ?>
                    </a>
                </div>

                <div class="menu">
                    <?php
                    $adminMenu = \Be\Be::getAdminMenu();
                    $adminMenuTree = $adminMenu->getTree();
                    $adminMenuActiveMenuKey = $adminMenu->getActiveMenuKey();
                    ?>
                    <el-menu
                            background-color="#30354d"
                            text-color="#aaa"
                            active-text-color="#fff"
                            :default-active="activeIndex"
                            :collapse="collapse"
                            :collapse-transition="false">
                        <?php
                        $appName = \Be\Be::getRequest()->getAppName();
                        foreach ($adminMenuTree as $item) {

                            if ($item->id === $appName) {

                                $hasSubItem = false;
                                if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                                    $hasSubItem = true;
                                }

                                // 有子菜单
                                if ($hasSubItem) {
                                    foreach ($item->subItems as $subItem) {

                                        $hasSubSubItem = false;
                                        if (isset($subItem->subItems) && is_array($subItem->subItems) && count($subItem->subItems) > 0) {
                                            $hasSubSubItem = true;
                                        }

                                        if ($hasSubSubItem) {
                                            echo '<el-submenu index="west-menu-'.$subItem->id.'" popper-class="be-west-popup-menu">';
                                            echo '<template slot="title">';
                                            echo '<i class="'.$subItem->icon.'"></i>';
                                            echo '<span>'.$subItem->label.'</span>';
                                            echo '</template>';

                                            foreach ($subItem->subItems as $subSubItem) {
                                                echo '<el-menu-item index="west-menu-'.$subSubItem->id.'">';
                                                echo '<template slot="title">';
                                                echo '<el-link href="'.$subSubItem->url.'" icon="'.$subSubItem->icon.'" :underline="false">';
                                                echo $subSubItem->label;
                                                echo '</el-link>';
                                                echo '</template>';
                                                echo '</el-menu-item>';
                                            }

                                            echo '</el-submenu>';
                                        } else {
                                            echo '<el-menu-item index="west-menu-'.$subItem->id.'">';
                                            echo '<template slot="title">';
                                            echo '<el-link href="'.$subItem->url.'" icon="'.$subItem->icon.'" :underline="false">';
                                            echo $subItem->label;
                                            echo '</el-link>';
                                            echo '</template>';
                                            echo '</el-menu-item>';
                                        }

                                    }
                                }
                                break;
                            }
                        }
                        ?>
                    </el-menu>
                </div>

                <div class="toggle" @click="toggleMenu">
                    <i :class="collapse ?'el-icon-s-unfold': 'el-icon-s-fold'"></i>
                </div>
            </div>
            <script>
                let westCollapseKey = 'be-admin-west-collapse';
                let vueWest = new Vue({
                    el: '#app-west',
                    data : {
                        activeIndex: "west-menu-<?php echo $adminMenuActiveMenuKey; ?>",
                        collapse: this.$cookies.isKey(westCollapseKey) && this.$cookies.get(westCollapseKey) === '1'
                    },
                    methods: {
                        toggleMenu: function (e) {
                            this.collapse = !this.collapse;
                            console.log(this.collapse);
                            document.getElementById("be-west").style.width = this.collapse ? "64px" : "200px";
                            document.getElementById("be-north").style.left = this.collapse ? "64px" : "200px";
                            document.getElementById("be-middle").style.marginLeft = this.collapse ? "64px" : "200px";
                            this.$cookies.set(westCollapseKey, this.collapse ? '1' : '0', 86400 * 180);
                        }
                    }
                });
            </script>
    <?php
    echo $this->tag1('be-west');
  }
}

public function north()
{
  if ($this->pageConfig->north !== 0) {
    echo $this->tag0('be-north');
    ?>
<div id="app-north" v-cloak>

                <div class="be-row">
                    <div class="be-col be-pl-200">
                        <?php
                        $adminMenu = \Be\Be::getAdminMenu();
                        $adminMenuTree = $adminMenu->getTree();
                        $adminMenuActiveMenuKey = $adminMenu->getActiveMenuKey();
                        ?>
                        <el-menu
                                mode="horizontal"
                                :default-active="defaultActive">
                            <?php
                            foreach ($adminMenuTree as $item) {

                                $hasSubItem = false;
                                if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                                    $hasSubItem = true;
                                }

                                // 有子菜单
                                if ($hasSubItem) {
                                    echo '<el-submenu index="north-menu-'.$item->id.'" popper-class="be-north-popup-menu">';

                                    echo '<template slot="title">';
                                    if ($item->url) {
                                        echo '<el-link href="'.$item->url.'" icon="'.$item->icon.'" :underline="false" style="display:inline !important;">';
                                        echo $item->label;
                                        echo '</el-link>';
                                    } else {
                                        echo '<i class="'.$item->icon.'"></i>';
                                        echo '<span>'.$item->label.'</span>';
                                    }
                                    echo '</template>';

                                    foreach ($item->subItems as $subItem) {

                                        $hasSubSubItem = false;
                                        if (isset($subItem->subItems) && is_array($subItem->subItems) && count($subItem->subItems) > 0) {
                                            $hasSubSubItem = true;
                                        }

                                        if ($hasSubSubItem) {
                                            echo '<el-submenu index="north-menu-'.$subItem->id.'" popper-class="be-north-popup-menu">';

                                            echo '<template slot="title">';
                                            if ($subItem->url) {
                                                echo '<el-link href="'.$subItem->url.'" icon="'.$subItem->icon.'" :underline="false">';
                                                echo $subItem->label;
                                                echo '</el-link>';
                                            } else {
                                                echo '<i class="'.$subItem->icon.'"></i>';
                                                echo '<span>'.$subItem->label.'</span>';
                                            }
                                            echo '</template>';

                                            foreach ($subItem->subItems as $subSubItem) {
                                                echo '<el-menu-item index="north-menu-'.$subSubItem->id.'">';
                                                echo '<el-link href="'.$subSubItem->url.'" icon="'.$subSubItem->icon.'" :underline="false">';
                                                echo $subSubItem->label;
                                                echo '</el-link>';
                                                echo '</el-menu-item>';
                                            }
                                            echo '</el-submenu>';
                                        } else {
                                            echo '<el-menu-item index="north-menu-'.$subItem->id.'">';
                                            echo '<el-link href="'.$subItem->url.'" icon="'.$subItem->icon.'" :underline="false">';
                                            echo $subItem->label;
                                            echo '</el-link>';
                                            echo '</el-menu-item>';
                                        }
                                    }
                                    echo '</el-submenu>';
                                }
                            }
                            ?>
                        </el-menu>
                    </div>

                    <div class="be-col-auto be-pr-150 north-links lh-60">
                        <el-link href="https://www.phpbe.com/doc/help/v2" icon="el-icon-warning-outline" target="_blank" :underline="false">使用帮助</el-link>
                    </div>

                    <div class="be-col-auto be-pr-150 lh-60">
                        <el-link href="<?php echo beUrl() ?>" icon="el-icon-view" target="_blank" :underline="false">预览网站</el-link>
                    </div>

                    <?php
                    $configUser = \Be\Be::getConfig('App.System.AdminUser');
                    $my = \Be\Be::getAdminUser();
                    ?>
                    <div class="be-col-auto be-pr-30 north-links lh-60">
                        <img src="<?php
                        if ($my->avatar === '') {
                            echo \Be\Be::getProperty('App.System')->getWwwUrl().'/admin/admin-user/images/avatar.png';
                        } else {
                            echo \Be\Be::getStorage()->getRootUrl() . '/app/system/admin-user/avatar/' . $my->avatar;
                        }
                        ?>" alt="" style="max-width:24px;max-height:24px; vertical-align: middle" >
                    </div>

                    <div class="be-col-auto be-pr-200 north-links" style="padding-top: 20px;">
                        <el-dropdown>
                            <span class="el-dropdown-link">
                                <!--i class="el-icon-user" style="font-size: 16px; vertical-align: middle;"></i-->
                                <?php echo $my->name; ?>
                                <i class="el-icon-arrow-down el-icon--right"></i>
                            </span>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item icon="el-icon-switch-button">
                                    <el-link href="<?php echo beAdminUrl('System.AdminUserLogin.logout'); ?>" :underline="false">退出登录</el-link>
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </div>

                </div>
            </div>
            <script>
                var vueNorth = new Vue({
                    el: '#app-north',
                    data: {
                        defaultActive: "north-menu-<?php echo $adminMenuActiveMenuKey; ?>",
                        aboutModel: false
                    },
                    methods: {

                    }
                });
            </script>
    <?php
    echo $this->tag1('be-north');
  }
}

public function middle()
{
  if ($this->pageConfig->middle !== 0 || $this->pageConfig->west !== 0 || $this->pageConfig->east !== 0 || $this->pageConfig->center !== 0) {
    echo $this->tag0('be-middle');
$this->center();
echo $this->tag1('be-middle');
  }
}

public function body()
{
    echo $this->tag0('be-body');
$this->north();
$this->west();
$this->middle();
echo $this->tag1('be-body');
}

}

