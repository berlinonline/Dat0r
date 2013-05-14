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
            Field\IntegerField::create('clickCount'),
            Field\TextField::create('author'),
            Field\TextField::create('email'),
            Field\IntegerCollectionField::create('images'),
            Field\TextCollectionField::create('keywords'),
            Field\BooleanField::create('enabled'),
            Field\AggregateField::create('paragraph', array(
                'aggregate_module' => '\Dat0r\Tests\Core\Runtime\Module\AggregateModule',
            )),
            Field\KeyValueField::create('meta', array(
                'constraints' => array('value_type' => 'dynamic',),
            )),
        ));
    }

    protected function getDocumentImplementor()
    {
        return 'Dat0r\\Tests\\Core\\Runtime\\Document\\DocumentTestProxy';
    }
}
