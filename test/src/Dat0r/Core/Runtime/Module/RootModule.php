<?php

namespace Dat0r\Tests\Core\Runtime\Module;

use Dat0r\Core\Runtime\Module;
use Dat0r\Core\Runtime\Field;

class RootModule extends Module\RootModule
{
    protected function __construct()
    {
        parent::__construct('Article', array( 
            Field\TextField::create('headline'),
            Field\TextField::create('content'), 
            Field\IntegerField::create('clickCount')
        ));
    }

    protected function getDocumentImplementor()
    {
        return 'Dat0r\\Tests\\Core\\Runtime\\Document\\DocumentTestProxy';
    }
}
