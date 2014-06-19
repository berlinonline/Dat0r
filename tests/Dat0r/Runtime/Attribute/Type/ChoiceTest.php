<?php

namespace Dat0r\Tests\Runtime\Attribute\Type;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Type\Choice;
use Dat0r\Runtime\Validator\Result\IIncident;

class ChoiceTest extends TestCase
{
    public function testCreate()
    {
        $select_attribute = $this->createAttribute();
        $this->assertEquals($select_attribute->getName(), 'city');
    }

    public function testCreateValue()
    {
        $select_attribute = $this->createAttribute();
        $value = $select_attribute->createValue();
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Value\\Type\\ChoiceValue', $value);
    }

    public function testValidationSuccess()
    {
        $select_attribute = $this->createAttribute();
        $value = $select_attribute->createValue();
        $result = $value->set('berlin');

        $this->assertEquals($result->getSeverity(), IIncident::SUCCESS);
        $this->assertEquals(array('berlin'), $value->get());
    }

    public function testValidationError()
    {
        $select_attribute = $this->createAttribute();
        $value = $select_attribute->createValue();
        $result = $value->set('meh');

        $this->assertEquals($result->getSeverity(), IIncident::ERROR);
        $this->assertEquals(null, $value->get());
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
