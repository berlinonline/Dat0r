<?php

namespace Dat0r\Tests\Core\Module;

class InvalidRootModule extends RootModule
{
    protected function getDocumentImplementor()
    {
        return 'NonExistantDocumentClass';
    }
}
