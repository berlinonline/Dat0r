<?php

namespace Dat0r\Tests\Runtime\Attribute\ValueHolder\Bundle;

use Mockery;
use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Type\Fixtures\ParagraphType;
use Dat0r\Runtime\Attribute\ValueHolder\Bundle\AggregateCollectionValueHolder;
use Dat0r\Runtime\Attribute\Bundle\AggregateCollection;
use Dat0r\Runtime\Document\DocumentList;

class AggregateCollectionValueHolderTest extends TestCase
{
    public function testCreate()
    {
        $value_holder = new AggregateCollectionValueHolder(
            new AggregateCollection(
                'paragraph',
                array(
                    'aggregates' => array('\\Dat0r\\Tests\\Runtime\\Type\\Fixtures\\ParagraphType'),
                )
            )
        );

        $this->assertInstanceOf(
            'Dat0r\\Runtime\\Attribute\\ValueHolder\\Bundle\\AggregateCollectionValueHolder',
            $value_holder
        );
    }

    public function testDefaultValue()
    {
        $aggregate_attribute = new AggregateCollection(
            'paragraph',
            array(
                'aggregates' => array('\\Dat0r\\Tests\\Runtime\\Type\\Fixtures\\ParagraphType'),
            )
        );

        $value_holder = $aggregate_attribute->createValueHolder();

        $document_list = $value_holder->getValue();
        $this->assertInstanceOf('Dat0r\\Runtime\\Document\\DocumentList', $document_list);
        $this->assertEquals(0, $document_list->getSize());
    }

    public function testValueChangedEvents()
    {
        $listener = Mockery::mock('\Dat0r\Runtime\Attribute\ValueHolder\IValueChangedListener');
        $listener->shouldReceive('onValueChanged')->with(
            '\Dat0r\Runtime\Attribute\ValueHolder\ValueChangedEvent'
        )->twice();

        $aggregate_type = new ParagraphType();
        $aggregated_document = $aggregate_type->createDocument(
            array('title' => 'Hello world', 'content' => 'Foobar lorem ipsum...')
        );

        $aggregate_attribute = new AggregateCollection(
            'paragraph',
            array(
                'aggregates' => array('\\Dat0r\\Tests\\Runtime\\Type\\Fixtures\\ParagraphType'),
            )
        );

        $value_holder = $aggregate_attribute->createValueHolder();
        $value_holder->addValueChangedListener($listener);

        $document_list = $value_holder->getValue();
        $document_list->push($aggregated_document);

        $aggregated_document->setValue('title', 'Kthxbye');

        $this->assertInstanceOf('Dat0r\\Runtime\\Document\\DocumentList', $document_list);
        $this->assertEquals(1, $document_list->getSize());
    }
}
