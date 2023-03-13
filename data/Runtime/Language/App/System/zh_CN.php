<?php
namespace Be\Data\Runtime\Language\App\System;


class zh_CN extends \Be\Language\Driver
{
  public string $package = 'App.System';
  public string $name = 'zh-CN';
  public array $keyValues = array (
  'RUNTIME.ROUTE_ERROR' => '跺由器（{0}）无法识别！',
  'RUNTIME.CONTROLLER_DOES_NOT_EXIST' => '应用（{0}）控制器（{1}）不存在！',
  'RUNTIME.UNDEFINED_ACTION' => '动作（{0}）在类（{1}）中不存在！',
  'PAGINATION.PREVIOUS' => '上一页',
  'PAGINATION.NEXT' => '下一页',
);
}

