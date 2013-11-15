<?php

namespace Dat0r\Tests\Runtime\ValueHolder;

use Mockery;
use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Module\Fixtures\AggregateModule;
use Dat0r\Runtime\ValueHolder\Type\AggregateValueHolder;
use Dat0r\Runtime\Field\Type\AggregateField;
use Dat0r\Runtime\Document\DocumentList;

class AggregateValueHolderTest extends TestCase
{
    public function testCreate()
    {
        $value_holder = AggregateValueHolder::create(
            AggregateField::create(
                'paragraph',
                array(
                    'modules' => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\AggregateModule'),
                )
            )
        );

        $this->assertInstanceOf('Dat0r\\Runtime\\ValueHolder\\Type\\AggregateValueHolder', $value_holder);
    }

    public function testDefaultValue()
    {
        $aggregate_field = AggregateField::create(
            'paragraph',
            array(
                'modules' => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\AggregateModule'),
            )
        );

        $value_holder = $aggregate_field->createValueHolder();

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

        $aggregate_module = AggregateModule::getInstance();
        $aggregated_document = $aggregate_module->createDocument(
            array('title' => 'Hello world', 'content' => 'Foobar lorem ipsum...')
        );

        $aggregate_field = AggregateField::create(
            'paragraph',
            array(
                'modules' => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\AggregateModule'),
            )
        );

        $value_holder = $aggregate_field->createValueHolder();
        $value_holder->addValueChangedListener($listener);

        $document_list = $value_holder->getValue();
        $document_list->push($aggregated_document);

        $aggregated_document->setValue('title', 'Kthxbye');

        $this->assertInstanceOf('Dat0r\\Runtime\\Document\\DocumentList', $document_list);
        $this->assertEquals(1, $document_list->getSize());
    }
}
