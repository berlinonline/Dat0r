<?php

namespace Dat0r\Tests\Runtime\Module\Fixtures;

use Dat0r\Runtime\Module\Module;
use Dat0r\Runtime\Field\Type\TextField;
use Dat0r\Runtime\Field\Type\TextCollectionField;
use Dat0r\Runtime\Field\Type\IntegerField;
use Dat0r\Runtime\Field\Type\IntegerCollectionField;
use Dat0r\Runtime\Field\Type\BooleanField;
use Dat0r\Runtime\Field\Type\AggregateField;
use Dat0r\Runtime\Field\Type\ReferenceField;
use Dat0r\Runtime\Field\Type\KeyValueField;

class RootModule extends Module
{
    protected function __construct()
    {
        parent::__construct(
            'Article',
            array(
                TextField::create('headline'),
                TextField::create('content'),
                IntegerField::create('clickCount'),
                TextField::create('author'),
                TextField::create('email'),
                IntegerCollectionField::create('images'),
                TextCollectionField::create('keywords'),
                BooleanField::create('enabled'),
                AggregateField::create(
                    'paragraph',
                    array(
                        'modules' => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\AggregateModule'),
                    )
                ),
                ReferenceField::create(
                    'references',
                    array(
                        ReferenceField::OPT_REFERENCES => array(
                            array(
                                ReferenceField::OPT_MODULE => '\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\RootModule',
                                ReferenceField::OPT_IDENTITY_FIELD => 'headline',
                                ReferenceField::OPT_DISPLAY_FIELD => 'headline'
                            ),
                        ),
                    )
                ),
                KeyValueField::create(
                    'meta',
                    array(
                        'constraints' => array('value_type' => 'dynamic',),
                    )
                ),
            )
        );
    }

    protected function getDocumentImplementor()
    {
        return '\\Dat0r\\Tests\\Runtime\\Document\\Fixtures\\DocumentTestProxy';
    }
}
