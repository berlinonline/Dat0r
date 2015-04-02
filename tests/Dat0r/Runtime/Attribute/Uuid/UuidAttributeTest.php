<?php

namespace Dat0r\Tests\Runtime\Attribute\Uuid;

use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\Attribute\Uuid\UuidAttribute;
use Dat0r\Runtime\Attribute\Uuid\UuidValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Tests\TestCase;

class UuidAttributeTest extends TestCase
{
    const FIELDNAME = 'uuid';
    const REGEX_UUID_V4 = '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i';

    public function testCreate()
    {
        $uuid_attribute = new UuidAttribute(self::FIELDNAME);
        $this->assertEquals($uuid_attribute->getName(), self::FIELDNAME);
    }

    public function testDefaultValue()
    {
        $uuid_attribute = new UuidAttribute(self::FIELDNAME);
        $default_value = $uuid_attribute->getDefaultValue();
        $this->assertFalse(empty($default_value));
        $this->assertTrue(is_string($default_value));

        $match_count = preg_match(self::REGEX_UUID_V4, $default_value);

        $this->assertTrue(1 === $match_count);
    }

    public function testInvalidValueSetting()
    {
        $attribute = new UuidAttribute(self::FIELDNAME);
        $valueholder = $attribute->createValueHolder();
        $this->assertTrue(1 === preg_match(self::REGEX_UUID_V4, $valueholder->getValue()));

        $result = $valueholder->setValue('asdf');
        $this->assertNotEquals('asdf', $valueholder->getValue());
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity());
        $this->assertTrue(1 === preg_match(self::REGEX_UUID_V4, $valueholder->getValue()));
    }

    public function testDefaultValueComparisonWorks()
    {
        $attribute = new UuidAttribute(self::FIELDNAME, [
            UuidAttribute::OPTION_DEFAULT_VALUE => 'f615154d-1657-463c-ae11-240590c55360'
        ]);

        $valueholder = $attribute->createValueHolder(true);
        $this->assertTrue(1 === preg_match(self::REGEX_UUID_V4, $valueholder->getValue()));
        $this->assertTrue($valueholder->isDefault());

        $result = $valueholder->setValue('asdf');
        $this->assertTrue($valueholder->isDefault());
    }

    public function testNullValueComparisonThrows()
    {
        $this->setExpectedException(RuntimeException::CLASS);
        $attribute = new UuidAttribute(self::FIELDNAME, [
            UuidAttribute::OPTION_DEFAULT_VALUE => 'f615154d-1657-463c-ae11-240590c55360'
        ]);

        $valueholder = $attribute->createValueHolder(true);
        $this->assertTrue(1 === preg_match(self::REGEX_UUID_V4, $valueholder->getValue()));
        $valueholder->isNull();
    }

    public function testDefaultValueComparisonThrowsWhenNoDefaultWasSet()
    {
        $this->setExpectedException(RuntimeException::CLASS);
        $attribute = new UuidAttribute(self::FIELDNAME);

        $valueholder = $attribute->createValueHolder();
        $this->assertTrue(1 === preg_match(self::REGEX_UUID_V4, $valueholder->getValue()));
        $valueholder->isDefault();
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $uuid_attribute = new UuidAttribute(self::FIELDNAME, $options);

        $this->assertEquals($uuid_attribute->getName(), self::FIELDNAME);
        $this->assertFalse($uuid_attribute->hasOption('snafu_flag'));

        foreach ($options as $optName => $optValue) {
            $this->assertTrue($uuid_attribute->hasOption($optName));
            $this->assertEquals($uuid_attribute->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getUuidAttributeFixture
     */
    public function testCreateValue($uuid)
    {
        $uuid_attribute = new UuidAttribute(self::FIELDNAME);
        $value = $uuid_attribute->createValueHolder();
        $this->assertInstanceOf(UuidValueHolder::CLASS, $value);
        $value->setValue($uuid);
        $this->assertEquals($uuid, $value->getValue());
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
    public static function getUuidAttributeFixture()
    {
        // @todo generate random (utf-8) text
        $fixtures = array();

        $fixtures[] = array('9303ecdb-016f-4942-837a-a20c97b64310');

        return $fixtures;
    }
}
