<?php

namespace Dat0r\Tests\Core\Runtime\Field;
use Dat0r\Tests\Core;

use Dat0r\Core\Runtime\Field;
use Dat0r\Core\Runtime\ValueHolder\UuidValueHolder;

class UuidFieldTest extends Core\BaseTest
{
    const FIELDNAME = 'test_uuid_field';

    public function testCreate()
    {
        $uuidField = Field\UuidField::create(self::FIELDNAME);
        $this->assertEquals($uuidField->getName(), self::FIELDNAME);
    }

    public function testDefaultValue()
    {
        $uuidField = Field\UuidField::create(self::FIELDNAME);
        $defaultValHolder = $uuidField->getDefaultValue();
        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\ValueHolder\\UuidValueHolder', $defaultValHolder);

        $default = $defaultValHolder->getValue();
        $this->assertFalse(empty($default));
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $uuidField = Field\UuidField::create(self::FIELDNAME, $options);

        $this->assertEquals($uuidField->getName(), self::FIELDNAME);
        $this->assertFalse($uuidField->hasOption('snafu_flag'));

        foreach ($options as $optName => $optValue)
        {
            $this->assertTrue($uuidField->hasOption($optName));
            $this->assertEquals($uuidField->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getUuidFixture
     */
    public function testCreateValueHolder($uuid)
    {
        $uuidField = Field\UuidField::create(self::FIELDNAME);
        $valueHolder = $uuidField->createValueHolder($uuid);
        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\ValueHolder\\UuidValueHolder', $valueHolder);
        $this->assertEquals($uuid, $valueHolder->getValue());
    }

    public function testValidate()
    {
        $uuidField = Field\UuidField::create(self::FIELDNAME);
        $this->assertTrue($uuidField->validate('09b2e257-efa1-4f79-83bc-12f05a92f531'));
        $this->assertFalse($uuidField->validate('this should not work!'));
        $this->assertFalse($uuidField->validate(23));
        $this->assertFalse($uuidField->validate(array('fnord' => 'array not acceptable')));
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