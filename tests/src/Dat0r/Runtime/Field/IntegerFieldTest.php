<?php

namespace Dat0r\Tests\Runtime\Field;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Field;

class IntegerFieldTest extends TestCase
{
    const FIELDNAME = 'test_int_field';

    public function testCreateField()
    {
        $integerField = Field\IntegerField::create(self::FIELDNAME);
        $this->assertEquals($integerField->getName(), self::FIELDNAME);
        $this->assertInstanceOf('Dat0r\\Runtime\\Field\\IntegerField', $integerField);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateFieldWithOptions(array $options)
    {
        $integerField = Field\IntegerField::create(self::FIELDNAME, $options);

        $this->assertEquals($integerField->getName(), self::FIELDNAME);
        $this->assertFalse($integerField->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue) {
            $this->assertTrue($integerField->hasOption($optName));
            $this->assertEquals($integerField->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getIntegerFixture
     */
    public function testCreateValueHolder($intValue)
    {
        $integerField = Field\IntegerField::create(self::FIELDNAME);
        $valueHolder = $integerField->createValueHolder();
        $this->assertInstanceOf('Dat0r\\Runtime\\ValueHolder\\IntegerValueHolder', $valueHolder);
        $valueHolder->setValue($intValue);
        $this->assertEquals($intValue, $valueHolder->getValue());
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
