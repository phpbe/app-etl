<?php
namespace Be\Data\Runtime\AdminTemplate\Blank\App\System\Admin\System;


class error extends \Be\Template\Driver
{
  public array $_tags = array (
  'be-body' => 
  array (
    0 => '<div class="be-body">',
    1 => '</div>',
  ),
  'be-north' => 
  array (
    0 => '<div class="be-north">',
    1 => '</div>',
  ),
  'be-middle' => 
  array (
    0 => '<div class="be-middle">',
    1 => '</div>',
  ),
  'be-west' => 
  array (
    0 => '<div class="be-west">',
    1 => '</div>',
  ),
  'be-center' => 
  array (
    0 => '<div class="be-center">',
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

    <link rel="stylesheet" href="<?php echo $appSystemWwwUrl; ?>/lib/font-awesome/font-awesome-4.7.0.min.css" />

    <link rel="stylesheet" href="//cdn.phpbe.com/ui/be.css" />

    <style>
        html {
            font-size: 14px;
            background-color: #fff;
            color: #333;
        }

        body {
            margin: 0;
            padding: 0;
        }

        [v-cloak] {display: none !important;}
        [class^="el-icon-fa"],
        [class*="el-icon-fa"] {
            display: inline-block;
            font-style: normal;
            font-variant: normal;
            font-weight: normal;
            font-family: FontAwesome!important;
            font-size: inherit;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>

    <?php $this->head(); ?>
</head>
<body>
<?php $this->body(); ?>
</body>
</html>
    <?php
  }

public function head()
{
    ?>
<link type="text/css" rel="stylesheet" href="<?php echo \Be\Be::getProperty('App.System')->getWwwUrl(); ?>/admin/system/css/error.css">
    <?php
}

public function body()
{
    echo $this->tag0('be-body');
    ?>
<div id="app" v-cloak>

        <div class="error-icon">
            <i class="el-icon-warning"></i>
        </div>

        <div class="error-message">
            <?php echo $this->message; ?>
        </div>

        <?php
        if (isset($this->redirect))
        {
            $redirectTimeout = $this->redirect['timeout'];
            if ($redirectTimeout > 0) {
                $redirectUrl = $this->redirect['url'];
                $redirectMessage = $this->redirect['message'] ?? '';
                if (!$redirectMessage) {
                    $redirectMessage = '{timeout} 秒后跳转到：{link}';
                }

                foreach ([
                             '{url}' => $redirectUrl,
                             '{link}' => '<el-link type="primary" href="' . $redirectUrl . '">' . $redirectUrl . '</el-link>',
                             '{timeout}' => '<span>{{redirectTimeout}}</span>',
                         ] as $key => $val) {
                    $redirectMessage = str_replace($key, $val, $redirectMessage);
                }

                echo '<div class="error-timer">' . $redirectMessage . '</div>';
            }
        }
        ?>
    </div>


    <script>
        new Vue({
            el: '#app',
            data: {
                redirectTimeout: <?php echo isset($this->redirect) ? $this->redirect['timeout'] : 0; ?>
            },
            created: function () {
                <?php
                if (isset($this->redirect)) {
                    $redirectUrl = $this->redirect['url'];
                    $redirectTimeout = $this->redirect['timeout'];
                    if ($redirectTimeout > 0) {
                        ?>
                        var _this = this;
                        var timer = setInterval(function () {
                            _this.redirectTimeout--;
                            if (_this.redirectTimeout <= 0) {
                                clearInterval(timer);
                                window.location.href = "<?php echo $redirectUrl; ?>";
                            }
                        }, 1000);
                        <?php
                    } else {
                        ?>
                        window.location.href = "<?php echo $redirectUrl; ?>";
                        <?php
                    }
                }
                ?>
            }
        });
    </script>
    <?php
    echo $this->tag1('be-body');
}

}

