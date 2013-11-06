<?php

namespace Dat0r\Tests\Core\ValueHolder;

use Dat0r\Tests\Core\BaseTest;

use Dat0r\Runtime\ValueHolder\AggregateValueHolder;
use Dat0r\Runtime\Field\AggregateField;
use Dat0r\Runtime\Document\DocumentList;

class AggregateValueHolderTest extends BaseTest
{
    public function testCreate()
    {
        $value_holder = AggregateValueHolder::create(
            AggregateField::create(
                'paragraph',
                array(
                    'modules' => array('\\Dat0r\\Tests\\Core\\Module\\Fixtures\\AggregateModule'),
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
                'modules' => array('\\Dat0r\\Tests\\Core\\Module\\Fixtures\\AggregateModule'),
            )
        );
        $value_holder = AggregateValueHolder::create($field, $field->getDefaultValue());
        $value = $value_holder->getValue();

        $this->assertInstanceOf('Dat0r\\Runtime\\Document\\DocumentList', $value);
        $this->assertEquals(0, $value->getSize());
    }
}
