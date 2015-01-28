<?php

namespace Dat0r\Tests\Runtime\Attribute\Choice;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Choice\ChoiceAttribute;
use Dat0r\Runtime\Attribute\Choice\ChoiceValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;

class ChoiceAttributeTest extends TestCase
{
    public function testCreate()
    {
        $select_attribute = $this->createAttribute();
        $this->assertEquals($select_attribute->getName(), 'city');
    }

    public function testCreateValue()
    {
        $select_attribute = $this->createAttribute();
        $value = $select_attribute->createValueHolder();
        $this->assertInstanceOf(ChoiceValueHolder::CLASS, $value);
    }

    public function testValidationSuccess()
    {
        $select_attribute = $this->createAttribute();
        $value = $select_attribute->createValueHolder();
        $result = $value->setValue('berlin');

        $this->assertEquals($result->getSeverity(), IncidentInterface::SUCCESS);
        $this->assertEquals(array('berlin'), $value->getValue());
    }

    public function testValidationError()
    {
        $select_attribute = $this->createAttribute();
        $value = $select_attribute->createValueHolder();
        $result = $value->setValue('meh');

        $this->assertEquals($result->getSeverity(), IncidentInterface::ERROR);
        $this->assertEquals(null, $value->getValue());
    }

    protected function createAttribute()
    {
        return new ChoiceAttribute(
            'city',
            array(
                'select_options' => array('new-york' => 'New York', 'berlin' => 'Berlin', 'boston' => 'Boston')
            )
        );
    }
}
