<?php

namespace Dat0r\Tests\Runtime\Attribute\Image;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\Image\Image;
use Dat0r\Runtime\Attribute\Image\ImageAttribute;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Tests\TestCase;
use stdClass;

class ImageAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new ImageAttribute('image');
        $this->assertEquals($attribute->getName(), 'image');
        $this->assertEquals(null, $attribute->getNullValue());
        $this->assertEquals(null, $attribute->getDefaultValue());
    }

    public function testValueComparison()
    {
        $img_data = [
            Image::PROPERTY_STORAGE_LOCATION => 'some.jpg',
            Image::PROPERTY_COPYRIGHT => 'some copyright string',
            Image::PROPERTY_META_DATA => [
                'foo' => 'bar',
                'leet' => 1337,
                'bool' => true
            ]
        ];

        $img2_data = $img_data;
        $img2_data[Image::PROPERTY_SOURCE] = 'some source';

        $attribute = new ImageAttribute('image', [ ImageAttribute::OPTION_DEFAULT_VALUE => $img_data ]);
        $valueholder = $attribute->createValueHolder();

        $this->assertInstanceOf(Image::CLASS, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($img_data));
        $this->assertFalse($valueholder->sameValueAs($img2_data));
    }

    public function testMetaDataValuesAreCastedToBeStringsIfConfigured()
    {
        $img_data = [
            Image::PROPERTY_STORAGE_LOCATION => 'some.jpg',
            Image::PROPERTY_META_DATA => [
                'foo' => 'bar',
                'leet' => 1337,
                'bool' => true
            ]
        ];

        $expected = [
            Image::PROPERTY_STORAGE_LOCATION => 'some.jpg',
            Image::PROPERTY_META_DATA => [
                'foo' => 'bar',
                'leet' => '1337',
                'bool' => '1'
            ]
        ];

        $attribute = new ImageAttribute('image', [
            ImageAttribute::OPTION_DEFAULT_VALUE => $img_data,
            ImageAttribute::OPTION_VALUE_TYPE => ImageAttribute::VALUE_TYPE_TEXT
        ]);
        $valueholder = $attribute->createValueHolder();

        $this->assertInstanceOf(Image::CLASS, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($expected));
    }

    public function testMetaDataValuesAreIntegerOnlyIfConfigured()
    {
        $img_data = [
            Image::PROPERTY_STORAGE_LOCATION => 'some.jpg',
            Image::PROPERTY_META_DATA => [
                'leet' => 1337,
                'foo' => -1337,
            ]
        ];
        $expected = $img_data;

        $attribute = new ImageAttribute('image', [
            ImageAttribute::OPTION_DEFAULT_VALUE => $img_data,
            ImageAttribute::OPTION_VALUE_TYPE => ImageAttribute::VALUE_TYPE_INTEGER
        ]);
        $valueholder = $attribute->createValueHolder();

        $this->assertInstanceOf(Image::CLASS, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($expected));
    }

    public function testRejectNonIntegerMetaDataValuesIfConfigured()
    {
        $img_data = [
            Image::PROPERTY_STORAGE_LOCATION => 'some.jpg',
            Image::PROPERTY_META_DATA => [
                'foo' => 'bar',
                'leet' => 1337,
                'bool' => true
            ]
        ];

        $attribute = new ImageAttribute('image', [
            ImageAttribute::OPTION_VALUE_TYPE => ImageAttribute::VALUE_TYPE_INTEGER
        ]);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($img_data);
        $this->assertNull($valueholder->getValue());
    }

    public function testToNativeRoundtrip()
    {
        $img_data = [
            Image::PROPERTY_STORAGE_LOCATION => 'some.jpg',
            Image::PROPERTY_COPYRIGHT => 'some copyright string',
            Image::PROPERTY_META_DATA => [
                'foo' => 'bar',
                'leet' => 1337,
                'bool' => true
            ]
        ];

        $native = [
            Image::PROPERTY_STORAGE_LOCATION => 'some.jpg',
            Image::PROPERTY_TITLE => '',
            Image::PROPERTY_CAPTION => '',
            Image::PROPERTY_COPYRIGHT => 'some copyright string',
            Image::PROPERTY_COPYRIGHT_URL => '',
            Image::PROPERTY_SOURCE => '',
            Image::PROPERTY_META_DATA => [
                'foo' => 'bar',
                'leet' => 1337,
                'bool' => true
            ]
        ];

        $attribute = new ImageAttribute('image', []);
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(Image::createFromArray($img_data));
        $this->assertInstanceOf(Image::CLASS, $valueholder->getValue());
        $this->assertEquals($native, $valueholder->toNative());
        $result = $valueholder->setValue($valueholder->toNative());
        $this->assertEquals(IncidentInterface::SUCCESS, $result->getSeverity());
        $this->assertInstanceOf(Image::CLASS, $valueholder->getValue());
        $this->assertEquals($native, $valueholder->toNative());
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);
        $attribute = new ImageAttribute('imageinvaliddefaultvalue', [
            ImageAttribute::OPTION_DEFAULT_VALUE => 5.00000001
        ]);
        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new ImageAttribute('imageInvalidValue');
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertGreaterThanOrEqual(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return array(
            array(null),
            array(3.14159),
            array(1337),
            array('foo'),
            array([]),
            array(false),
            array(true),
            array(new stdClass()),
            array(
                [
                    Image::PROPERTY_STORAGE_LOCATION => 'sadf.jpg',
                    Image::PROPERTY_COPYRIGHT_URL => 'localhost'
                ]
            ),
            array(
                [
                    Image::PROPERTY_STORAGE_LOCATION => 'sadf.jpg',
                    Image::PROPERTY_COPYRIGHT_URL => 'http://..com'
                ]
            ),
            array(
                [
                    Image::PROPERTY_STORAGE_LOCATION => 'sadf.jpg',
                    Image::PROPERTY_COPYRIGHT_URL => 'javascript:alert(1)'
                ]
            ),
        );
    }
}
