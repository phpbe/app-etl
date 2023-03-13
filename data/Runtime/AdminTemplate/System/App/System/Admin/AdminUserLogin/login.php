<?php
namespace Be\Data\Runtime\AdminTemplate\System\App\System\Admin\AdminUserLogin;


class login extends \Be\Template\Driver
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
<link type="text/css" rel="stylesheet" href="<?php echo \Be\Be::getProperty('App.System')->getWwwUrl(); ?>/admin/admin-user-login/css/login.css?v=20220711" />
    <?php
}

public function body()
{
    echo $this->tag0('be-body');
$config = \Be\Be::getConfig('App.System.System');
?>
<div id="app" v-cloak>

    <div class="logo">
        <?php
        $configTheme = \Be\Be::getConfig('AdminTheme.System.Theme');
        if ($configTheme->logo !== '') {
            echo '<img src="' . $configTheme->logo . '">';
        } else {
            ?>
            <svg viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg">
                <rect rx="5" height="40" width="40" x="10" y="10" fill="#ff5c35"/>
                <path d="M16 29 L21 29 M21 42 L16 42 L16 17 L21 17 C30 17 30 29 21 29 C30 30 30 42 21 42 M45 17 L34 17 L34 42 L46 42 M35 29 L44 29" stroke="#ffffff" stroke-width="2" fill="none" />
                <text x="65" y="35" style="font-size: 14px;"><tspan fill="#ff5c35">B</tspan><tspan fill="#999999">eyound</tspan> <tspan fill="#ff5c35">E</tspan><tspan fill="#999999">xception</tspan></text>
            </svg>
            <?php
        }
        ?>
    </div>

    <div class="login-box">
        <el-form size="small" layout="horizontal" ref="loginForm" :model="formData" label-width="80px">
            <el-form-item label="用户名" prop="username">
                <el-input v-model="formData.username" placeholder="用户名" prefix-icon="el-icon-user" clearable></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="password">
                <el-input v-model="formData.password" placeholder="密码" prefix-icon="el-icon-lock" show-password clearable></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" native-type="submit" @click="login" :loading="loginLoading">
                    <el-icon type="unlock"></el-icon>登录
                </el-button>
                <el-button @click="resetForm">重置</el-button>
            </el-form-item>
        </el-form>
    </div>

</div>

<?php
$return = \Be\Be::getRequest()->get('return', '');
if ($return=='') {
    $return = beAdminUrl('System.Home.index');
} else {
    $return = base64_decode($return);
}
?>
<script>
    new Vue({
        el: '#app',
        data: {
            formData: {
                username : "",
                password : ""
            },
            loginLoading: false
        },
        methods: {
            login: function() {
                var _this = this;
                _this.loginLoading = true;
                this.$http.post("<?php echo beAdminUrl('System.AdminUserLogin.login'); ?>", _this.formData)
                    .then(function (response) {
                        _this.loginLoading = false;
                        if (response.status === 200) {
                            if (response.data.success) {
                                window.location.href = "<?php echo $return; ?>";
                            } else {
                                _this.$message.error(response.data.message);
                            }
                        }
                    })
                    .catch(function (error) {
                        _this.loginLoading = false;
                        _this.$message.error(error);
                    });

            },
            resetForm: function () {
                this.$refs["loginForm"].resetFields();
            }
        }
    });
</script>
    <?php
    echo $this->tag1('be-body');
}

}

