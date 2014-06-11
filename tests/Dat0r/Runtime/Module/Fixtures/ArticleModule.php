<?php

namespace Dat0r\Tests\Runtime\Module\Fixtures;

use Dat0r\Runtime\Module\RootModule;
use Dat0r\Runtime\Field\Type\TextField;
use Dat0r\Runtime\Field\Type\TextCollectionField;
use Dat0r\Runtime\Field\Type\IntegerField;
use Dat0r\Runtime\Field\Type\IntegerCollectionField;
use Dat0r\Runtime\Field\Type\BooleanField;
use Dat0r\Runtime\Field\Type\AggregateField;
use Dat0r\Runtime\Field\Type\ReferenceField;
use Dat0r\Runtime\Field\Type\KeyValueField;

class ArticleModule extends RootModule
{
    public function __construct()
    {
        parent::__construct(
            'Article',
            array(
                new TextField('headline', array('min' => 4)),
                new TextField('content'),
                new IntegerField('clickCount'),
                new TextField('author'),
                new TextField('email'),
                new IntegerCollectionField('images'),
                new TextCollectionField('keywords'),
                new BooleanField('enabled'),
                new AggregateField(
                    'paragraph',
                    array(
                        'modules' => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\ParagraphModule'),
                    )
                ),
                new ReferenceField(
                    'references',
                    array(
                        'references' => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\CategoryModule'),
                    )
                ),
                new KeyValueField(
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
