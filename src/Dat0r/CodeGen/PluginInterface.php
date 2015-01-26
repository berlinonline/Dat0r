<?php

namespace Dat0r\CodeGen;

use Dat0r\CodeGen\Schema\TypeSchema;

interface PluginInterface
{
    public function execute(TypeSchema $schema);
}
