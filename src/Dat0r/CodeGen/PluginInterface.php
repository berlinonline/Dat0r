<?php

namespace Dat0r\CodeGen;

use Dat0r\CodeGen\Schema\EntityTypeSchema;

interface PluginInterface
{
    public function execute(EntityTypeSchema $schema);
}
