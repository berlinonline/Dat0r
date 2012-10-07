<?php

namespace Dat0r\Tests\Core\Runtime\Module;

use Dat0r\Core\Runtime\Module;

class RootModule extends Module\RootModule
{
    protected function getDocumentImplementor()
    {
        return 'Dat0r\Tests\Core\Runtime\Document\DocumentTestProxy';
    }
}
