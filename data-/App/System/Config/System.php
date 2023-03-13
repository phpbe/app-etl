<?php
namespace Be\Data\App\System\Config;

class System
{
  public string $rootUrl = '';
  public string $urlRewrite = 'disable';
  public string $urlSuffix = '.html';
  public string $uploadMaxSize = '100M';
  public ?array $allowUploadFileTypes = null;
  public ?array $allowUploadImageTypes = null;
  public string $timezone = 'Asia/Shanghai';
  public string $home = 'System.Home.index';
  public int $developer = 1;
  public int $installable = 0;

  public function __construct() {
    $this->allowUploadFileTypes = ['jpg','jpeg','gif','png','svg','webp','txt','pdf','doc','docx','csv','xls','xlsx','ppt','pptx','zip','rar','ttf','woff',];
    $this->allowUploadImageTypes = ['jpg','jpeg','gif','png','svg','webp',];
  }

}
