<?php

$baseDir = dirname(dirname(__FILE__));

// autoload vendor libs
$autoloadPath = array($baseDir, 'vendor', 'autoload.php');
require_once implode(DIRECTORY_SEPARATOR, $autoloadPath);

// autoload cmf codegeneration
$cmfAutoloaderPath = array($baseDir, 'lib', 'Dat0r', 'Autoloader.php');
require_once implode(DIRECTORY_SEPARATOR, $cmfAutoloaderPath);

Dat0r\Autoloader::register();
Dat0r\Core\CodeGenerator\CliInterface::run();
