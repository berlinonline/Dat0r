<?php

namespace Dat0r\Tests\Runtime\ValueHolder;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\ValueHolder\AggregateValueHolder;
use Dat0r\Runtime\Field\AggregateField;
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

        $this->assertInstanceOf('Dat0r\\Runtime\\ValueHolder\\AggregateValueHolder', $value_holder);
    }

    public function testDefaultValue()
    {
        $field = AggregateField::create(
            'paragraph',
            array(
                'modules' => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\AggregateModule'),
            )
        );
        $value_holder = $field->createValueHolder();
        $value_holder->setValue($field->getDefaultValue());
        $value = $value_holder->getValue();

        $this->assertInstanceOf('Dat0r\\Runtime\\Document\\DocumentList', $value);
        $this->assertEquals(0, $value->getSize());
    }
}
