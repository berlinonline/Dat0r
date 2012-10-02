<?php

namespace CMF\Tests\Core\Runtime\Module;
use CMF\Tests\Core\Runtime;

use CMF\Core\Runtime\Module;

class RootModuleTestProxy extends Module\RootModule
{
    protected function __construct($name, array $fields)
    {
        return parent::__construct($name, $fields);
    }

    protected function getDocumentImplementor()
    {
        return 'CMF\Tests\Core\Runtime\Document\DocumentTestProxy';
    }
}
