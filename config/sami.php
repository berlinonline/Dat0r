#!/usr/bin/env php
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

// return SAMI configuration for generation of API documentation
return new Sami\Sami($baseDir . '/src/Dat0r', array(
    'title'                => 'Dat0r API',
    'theme'                => 'enhanced',
    'default_opened_level' => 2,
    'build_dir'            => __DIR__.'/../build/docs/',
    'cache_dir'            => __DIR__.'/../build/cache',
));
