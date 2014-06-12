<?php

namespace Dat0r\CodeGen;

use Dat0r\Common\Entity\Configurable;
use Dat0r\CodeGen\Schema\TypeSchema;
use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Common\Error\FileSystemException;

interface IPlugin
{
    public function execute(TypeSchema $schema);
}
