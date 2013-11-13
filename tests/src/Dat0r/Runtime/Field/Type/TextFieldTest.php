<?php

namespace Dat0r\Tests\Runtime\Field;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Field\Type\TextField;
use Dat0r\Runtime\Validation\Result\IIncident;

class TextFieldTest extends TestCase
{
    const FIELDNAME = 'test_text_field';

    public function testCreate()
    {
        $textField = TextField::create(self::FIELDNAME);
        $this->assertEquals($textField->getName(), self::FIELDNAME);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $textField = TextField::create(self::FIELDNAME, $options);

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
        $textField = TextField::create(self::FIELDNAME);
        $valueHolder = $textField->createValueHolder();
        $this->assertInstanceOf('Dat0r\\Runtime\\ValueHolder\\Type\\TextValueHolder', $valueHolder);
        $valueHolder->setValue($textValue);
        $this->assertEquals($textValue, $valueHolder->getValue());
    }

    public function testValidationSuccess()
    {
        $text_field = TextField::create(
            self::FIELDNAME,
            array('min' => 3, 'max' => 10)
        );

        $result = $text_field->getValidator()->validate('erpen derp');
        $this->assertEquals($result->getSeverity(), IIncident::SUCCESS);
    }

    public function testValidationError()
    {
        $text_field = TextField::create(
            self::FIELDNAME,
            array('min' => 3, 'max' => 5)
        );

        $result = $text_field->getValidator()->validate('erpen derp');
        $this->assertEquals($result->getSeverity(), IIncident::ERROR);
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
