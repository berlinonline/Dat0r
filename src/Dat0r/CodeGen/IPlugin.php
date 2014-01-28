<?php

namespace Dat0r\CodeGen;

use Dat0r\Common\Configurable;
use Dat0r\CodeGen\Schema\ModuleSchema;
use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Common\Error\FilesystemException;

interface IPlugin
{
    public function execute(ModuleSchema $schema, Config $config);
}
