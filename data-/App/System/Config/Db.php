<?php
namespace Be\Data\App\System\Config;

class Db
{
  public ?array $master = null;

  public function __construct() {
    $this->master = ['driver' => 'mysql','host' => '127.0.0.1','port' => '3306','username' => 'root','password' => 'root','name' => 'be_app_etl','charset' => 'UTF8','pool' => '0',];
  }

}
