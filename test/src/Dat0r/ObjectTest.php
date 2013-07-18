<?php

namespace Dat0r\Tests;

use Dat0r\Tests\Fixtures;

// @todo test IObject implementations as value (recursively nad flat)
class ObjectTest extends BaseTestCase
{
    public function testCreate()
    {
        $property_one = 'value one';
        $property_two = 2;
        $property_three = false;

        $test_object = Fixtures\TestObject::create(array(
            'property_one' => $property_one,
            'property_two' => $property_two,
            'property_three' => $property_three
        ));

        $this->assertInstanceOf('\\Dat0r\\IObject', $test_object);
        $this->assertInstanceOf('\\Dat0r\\Tests\\Fixtures\\TestObject', $test_object);
        $this->assertEquals($property_one, $test_object->getPropertyOne());
        $this->assertEquals($property_two, $test_object->getPropertyTwo());
        $this->assertEquals($property_three, $test_object->getPropertyThree());
    }

    public function testToArray()
    {
        $object_data = array(
            'property_one' => 'wat?! I can haz valuez?',
            'property_two' => 3.0,
            'property_three' => true
        );

        $test_object = Fixtures\TestObject::create($object_data);

        $this->assertEquals($object_data, $test_object->toArray());
    }
}
