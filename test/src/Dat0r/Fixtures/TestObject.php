<?php

namespace Dat0r\Tests\Fixtures;

use Dat0r;

class TestObject extends Dat0r\Object
{
    protected $property_one;

    protected $property_two;

    protected $property_three;

    public function getPropertyOne()
    {
        return $this->property_one;
    }

    public function getPropertyTwo()
    {
        return $this->property_two;
    }

    public function getPropertyThree()
    {
        return $this->property_three;
    }
}
