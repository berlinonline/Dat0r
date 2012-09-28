<?php

// autoload vendor libs
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// autoload cmf codegeneration
$autoloadPathParts = array(dirname(dirname(__FILE__)), 'lib', 'CMF', 'Core', 'CodeGenerator', 'Autoloader.class.php');
require_once implode(DIRECTORY_SEPARATOR, $autoloadPathParts);

CMF\Core\CodeGenerator\Autoloader::register();
CMF\Core\CodeGenerator\CliInterface::run();
