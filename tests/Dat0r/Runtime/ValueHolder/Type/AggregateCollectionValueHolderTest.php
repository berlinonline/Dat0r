<?php

namespace Dat0r\Tests\Runtime\ValueHolder;

use Mockery;
use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Module\Fixtures\ParagraphModule;
use Dat0r\Runtime\ValueHolder\Type\AggregateCollectionValueHolder;
use Dat0r\Runtime\Attribute\Type\AggregateCollection;
use Dat0r\Runtime\Document\DocumentList;

class AggregateCollectionValueHolderTest extends TestCase
{
    public function testCreate()
    {
        $value_holder = AggregateCollectionValueHolder::create(
            new AggregateCollection(
                'paragraph',
                array(
                    'modules' => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\ParagraphModule'),
                )
            )
        );

        $this->assertInstanceOf('Dat0r\\Runtime\\ValueHolder\\Type\\AggregateCollectionValueHolder', $value_holder);
    }

    public function testDefaultValue()
    {
        $aggregate_attribute = new AggregateCollection(
            'paragraph',
            array(
                'modules' => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\ParagraphModule'),
            )
        );

        $value_holder = $aggregate_attribute->createValueHolder();

        $document_list = $value_holder->getValue();
        $this->assertInstanceOf('Dat0r\\Runtime\\Document\\DocumentList', $document_list);
        $this->assertEquals(0, $document_list->getSize());
    }

    public function testValueChangedEvents()
    {
        $listener = Mockery::mock('\Dat0r\Runtime\ValueHolder\IValueChangedListener');
        $listener->shouldReceive('onValueChanged')->with(
            '\Dat0r\Runtime\ValueHolder\ValueChangedEvent'
        )->twice();

        $aggregate_module = new ParagraphModule();
        $aggregated_document = $aggregate_module->createDocument(
            array('title' => 'Hello world', 'content' => 'Foobar lorem ipsum...')
        );

        $aggregate_attribute = new AggregateCollection(
            'paragraph',
            array(
                'modules' => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\ParagraphModule'),
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
