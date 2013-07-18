<?php

namespace Dat0r\Tests;

use Faker;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $faker;

    public function setUp()
    {
        $this->faker = Faker\Factory::create();
    }
}
