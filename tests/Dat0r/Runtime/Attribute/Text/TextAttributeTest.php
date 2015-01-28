<?php

namespace Dat0r\Tests\Runtime\Attribute\Text;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Attribute\Text\TextValueHolder;
use Dat0r\Runtime\Validator\Result\IncidentInterface;

class TextAttributeTest extends TestCase
{
    const FIELDNAME = 'test_text_attribute';

    public function testCreate()
    {
        $text_attribute = new TextAttribute(self::FIELDNAME);
        $this->assertEquals($text_attribute->getName(), self::FIELDNAME);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $text_attribute = new TextAttribute(self::FIELDNAME, $options);

        $this->assertEquals($text_attribute->getName(), self::FIELDNAME);
        $this->assertFalse($text_attribute->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue) {
            $this->assertTrue($text_attribute->hasOption($optName));
            $this->assertEquals($text_attribute->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getTextFixture
     */
    public function testCreateValue($textValue)
    {
        $text_attribute = new TextAttribute(self::FIELDNAME);
        $value = $text_attribute->createValueHolder();
        $this->assertInstanceOf(TextValueHolder::CLASS, $value);
        $value->setValue($textValue);
        $this->assertEquals($textValue, $value->getValue());
    }

    public function testValidationSuccess()
    {
        $text_attribute = new TextAttribute(
            self::FIELDNAME,
            array('min' => 3, 'max' => 10)
        );

        $result = $text_attribute->getValidator()->validate('erpen derp');
        $this->assertEquals($result->getSeverity(), IncidentInterface::SUCCESS);
    }

    public function testValidationError()
    {
        $text_attribute = new TextAttribute(
            self::FIELDNAME,
            array('min' => 3, 'max' => 5)
        );

        $result = $text_attribute->getValidator()->validate('erpen derp');
        $this->assertEquals($result->getSeverity(), IncidentInterface::ERROR);
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
