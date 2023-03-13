<?php
namespace Be\Data\Runtime\Template\Blank\App\System\System;


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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->title; ?></title>
    <base href="<?php echo beUrl(); ?>/">
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdn.phpbe.com/ui/be.css" />
    <link rel="stylesheet" href="https://cdn.phpbe.com/ui/be-icons.css"/>

    <style type="text/css">
        html {
            font-size: 14px;
            background-color: #fff;
            color: #333;
        }

        a {
            color:  #1f7df8;
        }

        a:hover {
            color: #ff5c35;
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

public function body()
{
    echo $this->tag0('be-body');
    ?>
<div class="be-p-100">

        <div class="be-ta-center be-c-red be-mt-300">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
            </svg>
        </div>

        <div class="be-ta-center be-fs-150 be-mt-300">
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
                    $redirectMessage = 'Redirect to {link} after {timeout} seconds.';
                }

                foreach ([
                             '{url}' => $redirectUrl,
                             '{link}' => '<a href="' . $redirectUrl . '">' . $redirectUrl . '</a>',
                             '{timeout}' => '<span id="redirect-timeout">' . $redirectTimeout . '</span>',
                         ] as $key => $val) {
                    $redirectMessage = str_replace($key, $val, $redirectMessage);
                }

                echo '<div class="be-ta-center be-c-999 be-mt-100">' . $redirectMessage . '</div>';
            }
        }
        ?>
    </div>

    <?php
    if (isset($this->redirect))
    {
        $redirectUrl = $this->redirect['url'];
        $redirectTimeout = $this->redirect['timeout'];
        if ($redirectTimeout > 0) {
            ?>
            <script>
                var redirectTimer = <?php echo $redirectTimeout; ?>;
                var timer = setInterval(function () {
                    redirectTimer--;
                    document.getElementById("redirect-timeout").innerHTML = redirectTimer;
                    if (redirectTimer <= 0) {
                        clearInterval(timer);
                        window.location.href = "<?php echo $redirectUrl; ?>";
                    }
                }, 1000);
            </script>
            <?php
        } else {
            ?>
            <script>window.location.href = "<?php echo $redirectUrl; ?>";</script>
            <?php
        }
    }
echo $this->tag1('be-body');
}

}

