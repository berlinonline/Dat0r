<?php

namespace Dat0r\Tests\Runtime\Module\Fixtures;

class InvalidRootModule extends RootModule
{
    protected function getDocumentImplementor()
    {
        return 'NonExistantDocumentClass';
    }
}
