<?php
namespace Be\Data\Runtime\AdminTemplate\Blank\App\System\Admin\System;


class exception extends \Be\Template\Driver
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
$configSystem = \Be\Be::getConfig('App.System.System');
if ($configSystem->developer) {
    $appSystemWwwUrl = \Be\Be::getProperty('App.System')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $appSystemWwwUrl; ?>/lib/google-code-prettify/prettify.css" type="text/css"/>
    <script type="text/javascript" language="javascript" src="<?php echo $appSystemWwwUrl; ?>/lib/google-code-prettify/prettify.js"></script>
    <style type="text/css">
        pre.prettyprint {
            background-color: #fff;
            color: #000;
            white-space: pre-wrap;
            word-wrap: break-word;
            border-color: #ddd;
        }
    </style>
    <?php
}
}

public function body()
{
    echo $this->tag0('be-body');
$configSystem = \Be\Be::getConfig('App.System.System');
if ($configSystem->developer) {
    $request = \Be\Be::getRequest();
    ?>
    <div id="app" v-cloak>
        <el-alert
                title="<?php echo htmlspecialchars($this->e->getMessage()); ?>"
                type="error"
                description="<?php if (isset($this->logId)) { echo '<div>#' . $this->logId . '</div>';} ?>"
                show-icon>
        </el-alert>

        <el-tabs v-model="activeTab" type="border-card" style="margin-top:10px;">
            <el-tab-pane label="错误跟踪信息" name="tab-trace">
                <pre class="prettyprint linenums"><?php print_r($this->e->getTrace()); ?></pre>
            </el-tab-pane>
            <el-tab-pane label="GET" name="tab-get">
                <pre class="prettyprint linenums"><?php print_r($request->get()) ?></pre>
            </el-tab-pane>
            <el-tab-pane label="POST" name="tab-post">
                <pre class="prettyprint linenums"><?php print_r($request->post()) ?></pre>
            </el-tab-pane>
            <el-tab-pane label="REQUEST" name="tab-request">
                <pre class="prettyprint linenums"><?php print_r($request->request()) ?></pre>
            </el-tab-pane>
            <el-tab-pane label="COOKIE" name="tab-cookie">
                <pre class="prettyprint linenums"><?php print_r($request->cookie()) ?></pre>
            </el-tab-pane>
            <el-tab-pane label="头信息" name="tab-server">
                <pre class="prettyprint linenums"><?php print_r($request->header()) ?></pre>
            </el-tab-pane>
            <el-tab-pane label="SERVER" name="tab-server">
                <pre class="prettyprint linenums"><?php print_r($request->server()) ?></pre>
            </el-tab-pane>
        </el-tabs>
    </div>
    <script>
        new Vue({
            el: '#app',
            data: {
                activeTab: 'tab-trace'
            },
            created: function () {
                prettyPrint();
            }
        });
    </script>
    <?php
} else {
    ?>
    <div class="be-ta-center be-c-red be-mt-300">
        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
        </svg>
    </div>

    <div class="be-ta-center be-fs-150 be-mt-300">
        <?php echo $this->e->getMessage(); ?>
    </div>
    <?php
}
echo $this->tag1('be-body');
}

}

