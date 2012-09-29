<?php

$baseDir = dirname(dirname(__FILE__));

// autoload vendor libs
$autoloadPath = array($baseDir, 'vendor', 'autoload.php');
require_once implode(DIRECTORY_SEPARATOR, $autoloadPath);

// autoload cmf codegeneration
$cmfAutoloaderPath = array($baseDir, 'lib', 'CMF', 'Autoloader.php');
require_once implode(DIRECTORY_SEPARATOR, $cmfAutoloaderPath);

CMF\Autoloader::register();
CMF\Core\CodeGenerator\CliInterface::run();
