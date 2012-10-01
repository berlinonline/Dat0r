<?php

namespace CMF\Tests\Core\Runtime\Field;
use CMF\Tests\Core\Runtime;

use CMF\Core\Runtime\Field;

class TextFieldTest extends Runtime\BaseTest
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
        $this->assertFalse($textField->hasOption('snafu_23'));
        foreach ($options as $optName => $optValue)
        {
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
        $this->assertInstanceOf('CMF\Core\Runtime\ValueHolder\TextValueHolder', $valueHolder);
        $this->assertEquals($textValue, $valueHolder->getValue());
    }

    public function testValidate()
    {
        $textField = Field\TextField::create(self::FIELDNAME);
        $this->assertTrue($textField->validate('some text value, if you foo what I bar.'));
        $this->assertFalse($textField->validate(234));
        $this->assertFalse($textField->validate(array('foo' => 'bar')));
    }

    public static function getOptionsFixture()
    {
        // @todo generate random options.
        $fixtures = array();

        $fixtures[] = array(
            array(
                'some_option_name' => 'some_option_value',
                'another_option_name' => 'another_option_value'
            )
        );

        return $fixtures;
    }

    public static function getTextFixture()
    {
        // @todo generate random (utf-8) text
        $fixtures = array();

        $fixtures[] = array('some text value');

        return $fixtures;
    }
}
