<?php

namespace Dat0r\Tests\Runtime\Type\Fixtures;

use Dat0r\Runtime\Type\AggregateRoot;
use Dat0r\Runtime\Attribute\Bundle\Text;
use Dat0r\Runtime\Attribute\Bundle\TextCollection;
use Dat0r\Runtime\Attribute\Bundle\Number;
use Dat0r\Runtime\Attribute\Bundle\NumberCollection;
use Dat0r\Runtime\Attribute\Bundle\Boolean;
use Dat0r\Runtime\Attribute\Bundle\AggregateCollection;
use Dat0r\Runtime\Attribute\Bundle\ReferenceCollection;
use Dat0r\Runtime\Attribute\Bundle\KeyValue;

class ArticleType extends AggregateRoot
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
                        'aggregates' => array('\\Dat0r\\Tests\\Runtime\\Type\\Fixtures\\ParagraphType'),
                    )
                ),
                new ReferenceCollection(
                    'references',
                    array(
                        'references' => array('\\Dat0r\\Tests\\Runtime\\Type\\Fixtures\\CategoryType'),
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
