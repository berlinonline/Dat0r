<?php

namespace Dat0r\Tests\Runtime\Attribute\Number;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Number\NumberAttribute;
use Dat0r\Runtime\Attribute\Number\NumberValueHolder;

class NumberAttributeTest extends TestCase
{
    const FIELDNAME = 'test_int_attribute';

    public function testCreateAttribute()
    {
        $number_attribute = new NumberAttribute(self::FIELDNAME);
        $this->assertEquals($number_attribute->getName(), self::FIELDNAME);
        $this->assertInstanceOf(NumberAttribute::CLASS, $number_attribute);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateAttributeWithOptions(array $options)
    {
        $number_attribute = new NumberAttribute(self::FIELDNAME, $options);

        $this->assertEquals($number_attribute->getName(), self::FIELDNAME);
        $this->assertFalse($number_attribute->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue) {
            $this->assertTrue($number_attribute->hasOption($optName));
            $this->assertEquals($number_attribute->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getIntegerFixture
     */
    public function testCreateValue($intValue)
    {
        $number_attribute = new NumberAttribute(self::FIELDNAME);
        $value = $number_attribute->createValueHolder();
        $this->assertInstanceOf(NumberValueHolder::CLASS, $value);
        $value->setValue($intValue);
        $this->assertEquals($intValue, $value->getValue());
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getOptionsFixture()
    {
        // @todo generate random options.
        $fixtures = array();

        $fixtures[] = array(
            array(
                'some_option_name' => 'some_option_value',
                'another_option_name' => 'another_option_value'
            ),
            array(
                'some_option_name' => 23,
                'another_option_name' => 5
            ),
            array(
                'some_option_name' => array('foo' => 'bar')
            )
        );

        return $fixtures;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getIntegerFixture()
    {
        // @todo generate random (utf-8) text
        $fixtures = array();

        $fixtures[] = array(2);
        $fixtures[] = array(23);
        $fixtures[] = array(2035);

        return $fixtures;
    }
}
