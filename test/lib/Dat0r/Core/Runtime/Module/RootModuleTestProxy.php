<?php

namespace Dat0r\Tests\Core\Runtime\Module;
use Dat0r\Tests\Core\Runtime;

use Dat0r\Core\Runtime\Module;

class RootModuleTestProxy extends Module\RootModule
{
    protected function __construct($name, array $fields)
    {
        return parent::__construct($name, $fields);
    }

    protected function getDocumentImplementor()
    {
        return 'Dat0r\Tests\Core\Runtime\Document\DocumentTestProxy';
    }
}
