<?php

namespace Dat0r\Tests\Core\Runtime\Module;

class InvalidRootModule extends RootModule
{
    protected function getDocumentImplementor()
    {
        return 'NonExistantDocumentClass';
    }
}
