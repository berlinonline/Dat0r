<?php

namespace Dat0r\Tests\Runtime\Attribute\Bundle;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Bundle\Choice;
use Dat0r\Runtime\Validator\Result\IIncident;

class ChoiceTest extends TestCase
{
    public function testCreate()
    {
        $select_attribute = $this->createAttribute();
        $this->assertEquals($select_attribute->getName(), 'city');
    }

    public function testCreateValueHolder()
    {
        $select_attribute = $this->createAttribute();
        $value_holder = $select_attribute->createValueHolder();
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\ValueHolder\\Bundle\\ChoiceValueHolder', $value_holder);
    }

    public function testValidationSuccess()
    {
        $select_attribute = $this->createAttribute();
        $value_holder = $select_attribute->createValueHolder();
        $result = $value_holder->setValue('berlin');

        $this->assertEquals($result->getSeverity(), IIncident::SUCCESS);
        $this->assertEquals(array('berlin'), $value_holder->getValue());
    }

    public function testValidationError()
    {
        $select_attribute = $this->createAttribute();
        $value_holder = $select_attribute->createValueHolder();
        $result = $value_holder->setValue('meh');

        $this->assertEquals($result->getSeverity(), IIncident::ERROR);
        $this->assertEquals(null, $value_holder->getValue());
    }

    protected function createAttribute()
    {
        return new Choice(
            'city',
            array(
                'select_options' => array('new-york' => 'New York', 'berlin' => 'Berlin', 'boston' => 'Boston')
            )
        );
    }
}
