<?php
namespace Be\Data\Runtime\AdminTemplate\Installer\App\System\Admin\Installer;


class setting extends \Be\Template\Driver
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
    <meta charset="utf-8"/>
    <title><?php echo $this->title; ?></title>
    <base href="<?php echo beUrl(); ?>/" >

    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({cache: false});

            $(document).ajaxStart(function(){
                $('#ajax-loader').fadeIn();
            }).ajaxStop(function(){
                $('#ajax-loader').fadeOut();
            });
        });
    </script>

    <link rel="stylesheet" href="https://cdn.phpbe.com/ui/be.css"/>
    <link rel="stylesheet" href="https://cdn.phpbe.com/ui/be-icons.css"/>

    <style type="text/css">
        html {
            font-size: 16px;
            background-color: #f6f9fc;
            color: #2e475d;
        }

        body {
        <?php
        $majorColor = '#ff5c35';
        echo '--major-color: ' . $majorColor . ';';

        // CSS 处理库
        $libCss = \Be\Be::getLib('Css');
        for ($i=1; $i<=9; $i++) {
            echo '--major-color-' . $i. ': ' . $libCss->lighter($majorColor, $i * 10) . ';';
            echo '--major-color' . $i. ': ' . $libCss->darker($majorColor, $i * 10) . ';';
        }
        ?>
        }

        @keyframes ajaxLoading {
            0% {
                transform: rotate(0deg)
            }

            100% {
                transform: rotate(360deg)
            }
        }

        #ajax-loader {
            position: fixed;
            top: calc(50% - 30px);
            left: calc(50% - 30px);
            width: 60px;
            height: 60px;
            z-index: 1050;
            background-color: rgba(255, 255, 255, .8);
            border-radius: 10px;
            display: none;
        }

        #ajax-loader div {
            position: absolute;
            top: 13px;
            left: 13px;
            width: 35px;
            height: 35px;
            border: 4px solid;
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
            -moz-animation: ajaxLoading 1.2s cubic-bezier(.5, 0, .5, 1) infinite;
            -webkit-animation: ajaxLoading 1.2s cubic-bezier(.5, 0, .5, 1) infinite;
            -o-animation: ajaxLoading 1.2s cubic-bezier(.5, 0, .5, 1) infinite;
            animation: ajaxLoading 1.2s cubic-bezier(.5, 0, .5, 1) infinite;
            border-color: var(--major-color) transparent transparent transparent;
        }

        #ajax-loader div:nth-child(1) {
            -moz-animation-delay: -0.45s;
            -webkit-animation-delay: -0.45s;
            -o-animation-delay: -0.45s;
            animation-delay: -0.45s
        }

        #ajax-loader div:nth-child(2) {
            -moz-animation-delay: -0.3s;
            -webkit-animation-delay: -0.3s;
            -o-animation-delay: -0.3s;
            animation-delay: -0.3s
        }

        #ajax-loader div:nth-child(3) {
            -moz-animation-delay: -0.15s;
            -webkit-animation-delay: -0.15s;
            -o-animation-delay: -0.15s;
            animation-delay: -0.15s
        }
    </style>

    <?php $this->head(); ?>
</head>
<body>
<?php $this->body(); ?>

<div id="ajax-loader">
    <div></div>
    <div></div>
    <div></div>
    <div></div>
</div>

</body>
</html>
    <?php
  }

public function head()
{
    ?>
<style>
        .be-input, .be-select {
            max-width: 300px;
        }

        .be-input[type="number"] {
            max-width:200px;
        }
    </style>
    <?php
}

public function center()
{
  if ($this->pageConfig->center !== 0) {
    echo $this->tag0('be-center');
    ?>
<form id="form" action="<?php echo \Be\Be::getRequest()->getUrl(); ?>" method="post">
        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250">
                超级管理员账号：
            </div>
            <div class="be-col">
                <input type="text" class="be-input" name="username" value="admin">
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250">
                密码：
            </div>
            <div class="be-col">
                <input type="text" class="be-input" name="password" value="admin">
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250">
                名称：
            </div>
            <div class="be-col">
                <input type="text" class="be-input" name="name" value="管理员">
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250">
                邮箱：
            </div>
            <div class="be-col">
                <input type="text" class="be-input" name="email" value="">
            </div>
        </div>

        <div class="be-mt-200 be-bt-eee be-pt-100 be-ta-center">
            <a class="be-btn" href="<?php echo beAdminUrl('System.Installer.installApp'); ?>">上一步</a>
            <input type="button" class="be-btn be-btn-major" value="完成安装" onclick="setting()">
        </div>

    </form>

    <script>
        function setting() {
            $.ajax({
                url: "<?php echo beAdminUrl('System.Installer.setting'); ?>",
                data : $("#form").serialize(),
                method: "POST",
                success: function (json) {
                    if (json.success) {
                        window.location.href = json.redirectUrl;
                    } else {
                        alert(json.message);
                    }
                },
                error: function () {
                    alert("系统错误!");
                }
            });
        }
    </script>
    <?php
    echo $this->tag1('be-center');
  }
}

public function body()
{
    echo $this->tag0('be-body');
    ?>
<div class="be-py-75" style="background-color: var(--major-color-9)">
        <div class="be-container">
            <div class="be-row">
                <div class="be-col-auto">
                    <a href="<?php echo beUrl(); ?>" class="be-d-inline-block">
                        <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                            <rect rx="8" width="64" height="64" y="0" x="0" fill="#ff5c35"/>
                            <path d="M9 31 L15 31 M15 52 L9 52 L9 11 L15 11 C32 11 32 31 15 31 C32 32 32 52 15 52 M57 11 L39 11 L39 52 L57 52 M39 31 L55 31" stroke="#ffffff" stroke-width="4" fill="none" />
                        </svg>
                    </a>
                </div>
                <div class="be-col-auto be-pl-125">
                    <div class="be-fs-125 be-lh-125 be-fw-100 be-mt-75"><span style="color: #f60;">B</span>eyond</div>
                    <div class="be-fs-125 be-lh-125 be-fw-100"><span class="be-pl-250" style="color: #f60;">E</span>xception</div>
                </div>
                <div class="be-col">
                    <div class="be-fs-150 be-lh-150 be-fw-100 be-mt-150 be-pl-250">BE双驱框架 - 安装程序</div>
                </div>
            </div>

        </div>
    </div>

    <div class="be-container be-my-200">
        <?php
        if (isset($this->steps) && is_array($this->steps) && count($this->steps) > 0) {
            $currentStep = isset($this->step) ? $this->step : 1;
            ?>
            <div class="be-row be-mb-200">
                <div class="be-col"></div>
                <?php
                $i = 1;
                foreach ($this->steps as $step) {
                    if ($currentStep < $i) {
                        $class = 'be-c-ccc';
                    } else if ($currentStep === $i) {
                        $class = 'be-c-major';
                    } else {
                        $class = 'be-c-green';
                    }
                    ?>
                    <div class="be-col-auto">
                        <div class="be-px-100">
                            <div class="be-ta-center be-fs-200 <?php echo $class; ?>"><?php echo $i; ?></div>
                            <div class="be-ta-center be-mt-50 <?php echo $class; ?>"><?php echo $step; ?></div>
                        </div>
                    </div>

                    <?php
                    $i++;
                }
                ?>
                <div class="be-col"></div>
            </div>
            <?php
        }
$this->center(); ?>
    </div>
    <?php
    echo $this->tag1('be-body');
}

}

