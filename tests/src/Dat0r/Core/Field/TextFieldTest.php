<?php

namespace Dat0r\Tests\Core\Field;

use Dat0r\Tests\Core;
use Dat0r\Runtime\Field;

class TextFieldTest extends Core\BaseTest
{
    const FIELDNAME = 'test_text_field';

    public function testCreate()
    {
        $textField = Field\TextField::create(self::FIELDNAME);
        $this->assertEquals($textField->getName(), self::FIELDNAME);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $textField = Field\TextField::create(self::FIELDNAME, $options);

        $this->assertEquals($textField->getName(), self::FIELDNAME);
        $this->assertFalse($textField->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue) {
            $this->assertTrue($textField->hasOption($optName));
            $this->assertEquals($textField->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getTextFixture
     */
    public function testCreateValueHolder($textValue)
    {
        $textField = Field\TextField::create(self::FIELDNAME);
        $valueHolder = $textField->createValueHolder($textValue);
        $this->assertInstanceOf('Dat0r\\Runtime\\ValueHolder\\TextValueHolder', $valueHolder);
        $this->assertEquals($textValue, $valueHolder->getValue());
    }

    public function testValidate()
    {
        $textField = Field\TextField::create(self::FIELDNAME);
        $this->assertTrue($textField->validate('this is a valid text value.'));
        $this->assertFalse($textField->validate(235));
        $this->assertFalse($textField->validate(array('fnord' => 'array not acceptable')));
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
    public static function getTextFixture()
    {
        // @todo generate random (utf-8) text
        $fixtures = array();

        $fixtures[] = array('some text value');

        return $fixtures;
    }
}
