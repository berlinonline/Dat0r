<?php

namespace Dat0r\Tests\Core\Module\Fixtures;

class InvalidRootModule extends RootModule
{
    protected function getDocumentImplementor()
    {
        return 'NonExistantDocumentClass';
    }
}
