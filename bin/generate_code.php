<?php

$baseDir = dirname(__DIR__);
if (! is_dir($baseDir . DIRECTORY_SEPARATOR . 'vendor'))
{
    $baseDir = dirname(dirname(dirname($baseDir)));
}
if (! is_dir($baseDir . DIRECTORY_SEPARATOR . 'vendor'))
{
    throw new Exception('Unable to locate vendor directory.');
}

// autoload vendor libs
$autoloadPath = array($baseDir, 'vendor', 'autoload.php');
require_once implode(DIRECTORY_SEPARATOR, $autoloadPath);

Dat0r\Autoloader::register();
Dat0r\Core\CodeGenerator\CliInterface::run();
