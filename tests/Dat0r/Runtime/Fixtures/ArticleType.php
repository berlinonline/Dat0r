<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Runtime\DocumentType;
use Dat0r\Runtime\Attribute\Type\Text;
use Dat0r\Runtime\Attribute\Type\TextCollection;
use Dat0r\Runtime\Attribute\Type\Number;
use Dat0r\Runtime\Attribute\Type\NumberCollection;
use Dat0r\Runtime\Attribute\Type\Boolean;
use Dat0r\Runtime\Attribute\Type\AggregateCollection;
use Dat0r\Runtime\Attribute\Type\ReferenceCollection;
use Dat0r\Runtime\Attribute\Type\KeyValue;

class ArticleType extends DocumentType
{
    public function __construct()
    {
        parent::__construct(
            'Article',
            array(
                new Text('headline', array('min' => 4)),
                new Text('content'),
                new Number('clickCount'),
                new Text('author'),
                new Text('email'),
                new NumberCollection('images'),
                new TextCollection('keywords'),
                new Boolean('enabled'),
                new AggregateCollection(
                    'paragraph',
                    array(
                        'aggregates' => array('\\Dat0r\\Tests\\Runtime\\Fixtures\\ParagraphType'),
                    )
                ),
                new ReferenceCollection(
                    'references',
                    array(
                        'references' => array('\\Dat0r\\Tests\\Runtime\\Fixtures\\CategoryType'),
                    )
                ),
                new KeyValue(
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
