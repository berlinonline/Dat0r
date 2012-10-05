<?php

namespace Dat0r\Tests\Core\Runtime\Module;

use Dat0r\Core\Runtime\Module;

class InvalidRootModule extends Module\RootModule
{
    protected function getDocumentImplementor()
    {
        return 'NonExistantDocumentClass';
    }
}
