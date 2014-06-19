<?php

namespace Dat0r\Tests\Runtime\Attribute\Type;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Type\Uuid;
use Dat0r\Runtime\Attribute\Value\UuidValue;

class UuidTest extends TestCase
{
    const FIELDNAME = 'test_uuid_attribute';

    public function testCreate()
    {
        $uuid_attribute = new Uuid(self::FIELDNAME);
        $this->assertEquals($uuid_attribute->getName(), self::FIELDNAME);
    }

    public function testDefaultValue()
    {
        $uuid_attribute = new Uuid(self::FIELDNAME);
        $default_value = $uuid_attribute->getDefaultValue();
        $this->assertFalse(empty($default_value));
        $this->assertTrue(is_string($default_value));

        $match_count = preg_match(
            '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
            '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i',
            $default_value
        );

        $this->assertTrue(1 === $match_count);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $uuid_attribute = new Uuid(self::FIELDNAME, $options);

        $this->assertEquals($uuid_attribute->getName(), self::FIELDNAME);
        $this->assertFalse($uuid_attribute->hasOption('snafu_flag'));

        foreach ($options as $optName => $optValue) {
            $this->assertTrue($uuid_attribute->hasOption($optName));
            $this->assertEquals($uuid_attribute->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getUuidFixture
     */
    public function testCreateValue($uuid)
    {
        $uuid_attribute = new Uuid(self::FIELDNAME);
        $value = $uuid_attribute->createValue();
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\Value\\Type\\UuidValue', $value);
        $value->set($uuid);
        $this->assertEquals($uuid, $value->get());
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
    public static function getUuidFixture()
    {
        // @todo generate random (utf-8) text
        $fixtures = array();

        $fixtures[] = array('9303ecdb-016f-4942-837a-a20c97b64310');

        return $fixtures;
    }
}
