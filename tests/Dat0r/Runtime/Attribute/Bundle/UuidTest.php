<?php

namespace Dat0r\Tests\Runtime\Attribute\Bundle;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Bundle\Uuid;
use Dat0r\Runtime\Attribute\ValueHolder\UuidValueHolder;

class UuidTest extends TestCase
{
    const FIELDNAME = 'test_uuid_attribute';

    public function testCreate()
    {
        $uuidAttribute = new Uuid(self::FIELDNAME);
        $this->assertEquals($uuidAttribute->getName(), self::FIELDNAME);
    }

    public function testDefaultValue()
    {
        $uuidAttribute = new Uuid(self::FIELDNAME);
        $defaultValue = $uuidAttribute->getDefaultValue();

        $this->assertFalse(empty($defaultValue));
        $this->assertTrue(is_string($defaultValue));

        $match_count = preg_match(
            '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
            '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i',
            $defaultValue
        );
        $this->assertTrue(1 === $match_count);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $uuidAttribute = new Uuid(self::FIELDNAME, $options);

        $this->assertEquals($uuidAttribute->getName(), self::FIELDNAME);
        $this->assertFalse($uuidAttribute->hasOption('snafu_flag'));

        foreach ($options as $optName => $optValue) {
            $this->assertTrue($uuidAttribute->hasOption($optName));
            $this->assertEquals($uuidAttribute->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getUuidFixture
     */
    public function testCreateValueHolder($uuid)
    {
        $uuidAttribute = new Uuid(self::FIELDNAME);
        $valueHolder = $uuidAttribute->createValueHolder();
        $this->assertInstanceOf('Dat0r\\Runtime\\Attribute\\ValueHolder\\Bundle\\UuidValueHolder', $valueHolder);
        $valueHolder->setValue($uuid);
        $this->assertEquals($uuid, $valueHolder->getValue());
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
