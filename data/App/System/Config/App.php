<?php
namespace Be\Data\App\System\Config;

class App
{
  public ?array $names = null;

  public function __construct() {
    $this->names = ['System','Etl',];
  }

}
