<?php
namespace Be\Data\Runtime\AdminTemplate\System\AdminPlugin\Config;


class display extends \Be\Template\Driver
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

public function head()
{
    ?>
<style type="text/css">
        .el-form-item .el-form-item {
            margin-bottom: 10px;
        }
    </style>
    <?php
}

public function pageContent()
{
    echo $this->tag0('be-page-content');
$formData = [];
    $uiItems = new \Be\AdminPlugin\UiItem\UiItems();
    ?>
    <div class="be-bc-fff be-px-150 be-pt-150 be-pb-50" id="app" v-cloak>

        <el-tabs tab-position="left" value="<?php echo $this->configName; ?>" @tab-click="goto">
            <?php
            foreach ($this->configs as $config) {
                ?>
                <el-tab-pane name="<?php echo $config['name']; ?>" label="<?php echo $config['label']; ?>">
                    <?php
                    if ($config['name'] === $this->configName) {
                        if (count($this->configItemDrivers)) {
                            ?>
                            <div style="max-width: 800px;">
                                <el-form size="medium" label-width="200px" :disabled="loading">
                                    <?php
                                    foreach ($this->configItemDrivers as $driver) {

                                        echo $driver->getHtml();

                                        if ($driver instanceof \Be\AdminPlugin\Form\Item\FormItems) {
                                            if ($driver->name !== null) {
                                                $formData[$driver->name] = $driver->value;
                                            }
                                        } else {
                                            if ($driver->name !== null) {
                                                if (is_array($driver->value) || is_object($driver->value)) {
                                                    $formData[$driver->name] =  json_encode($driver->value, JSON_PRETTY_PRINT);
                                                } else {
                                                    $formData[$driver->name] = $driver->value;
                                                }
                                            }
                                        }

                                        $uiItems->add($driver);
                                    }
                                    ?>
                                    <el-form-item>
                                        <el-button type="primary" icon="el-icon-check" @click="saveConfig">保存</el-button>
                                        <el-button type="warning" icon="el-icon-close" @click="resetConfig">恢复默认值</el-button>
                                        <?php if (isset($config['test'])) { ?>
                                            <el-button icon="el-icon-view" @click="window.open('<?php echo $config['test']; ?>');">测试</el-button>
                                        <?php } ?>
                                    </el-form-item>
                                </el-form>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </el-tab-pane>
                <?php
            }
            ?>
        </el-tabs>
    </div>

    <?php
    echo $uiItems->getJs();
    echo $uiItems->getCss();
    ?>

    <script>
        var vueForm = new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>,
                loading: false
                <?php
                echo $uiItems->getVueData();
                ?>
            },
            methods: {
                saveConfig: function () {
                    this.loading = true;
                    var _this = this;
                    _this.$http.post("<?php echo beAdminUrl(null, ['task' => 'saveConfig', 'configName' => $this->configName]); ?>", {
                        formData: _this.formData
                    }).then(function (response) {
                            _this.loading = false;
                            if (response.status === 200) {
                                if (response.data.success) {
                                    _this.$message.success(response.data.message);
                                } else {
                                    _this.$message.error(response.data.message);
                                }
                            }
                        }).catch(function (error) {
                        _this.loading = false;
                        _this.$message.error(error);
                    });
                },

                resetConfig: function () {
                    var _this = this;
                    this.$confirm('该操作不可恢复，确认恢复默认值吗？', '确认恢复默认值吗', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(function () {
                        _this.loading = true;
                        _this.$http.get("<?php echo beAdminUrl(null, ['task' => 'resetConfig', 'configName' => $this->configName]); ?>")
                            .then(function (response) {
                                _this.loading = false;
                                if (response.status === 200) {
                                    if (response.data.success) {
                                        _this.$message.success(response.data.message);
                                        window.location.reload();
                                    } else {
                                        _this.$message.error(response.data.message);
                                    }
                                }
                            })
                            .catch(function (error) {
                                _this.loading = false;
                                _this.$message.error(error);
                            });
                    }).catch(function () {
                        _this.loading = false;
                    });
                },
                goto: function (tab) {
                    var sUrl = "<?php echo beAdminUrl(\Be\Be::getRequest()->getRoute()); ?>";
                    sUrl += sUrl.indexOf("?") >= 0 ? "&" : "?";
                    sUrl += "configName=" + tab.name;
                    window.location.href = sUrl;
                }

                <?php
                echo $uiItems->getVueMethods();
                ?>
            }

            <?php
            echo $uiItems->getVueHooks();
            ?>
        });
    </script>
    <?php
    echo $this->tag1('be-page-content');
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

