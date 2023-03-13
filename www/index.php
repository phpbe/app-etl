<?php
$rootPath = dirname(__DIR__);
//$rootPath =  dirname($_SERVER['DOCUMENT_ROOT']);

$loader = require($rootPath . '/vendor/autoload.php');
$loader->addPsr4('Be\\Data\\', $rootPath . '/data');

$runtime = new \Be\Runtime\Driver\Common();
$runtime->setRootPath($rootPath);
$runtime->setAdminAlias('admin');
\Be\Be::setRuntime($runtime);
$runtime->execute();
