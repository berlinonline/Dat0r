<?php

namespace Dat0r\Tests\Fixtures;

use Dat0r;
use Faker;

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

    public static function createRandomInstances()
    {
        $faker = Faker\Factory::create();

        $test_objects = array();
        $max = $faker->randomNumber(1, 15);

        for ($i = 0; $i < $max; $i++)
        {
            $test_objects[] = static::create(
                array(
                    'property_one' => $faker->word(23),
                    'property_two' => $faker->randomNumber(0, 500),
                    'property_three' => $faker->boolean()
                )
            );
        }

        return $test_objects;
    }
}
