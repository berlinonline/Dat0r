<?php

namespace Dat0r\Tests\Runtime\Module\Fixtures;

use Dat0r\Runtime\Module\RootModule;

class InvalidModule extends RootModule
{
    public function __construct()
    {
        parent::__construct('InvalidModule');
    }

    protected function getDocumentImplementor()
    {
        return 'NonExistantDocumentClass';
    }
}
