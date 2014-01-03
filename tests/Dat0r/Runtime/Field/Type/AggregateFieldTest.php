<?php

namespace Dat0r\Tests\Runtime\Field;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Field\Type\AggregateField;

class AggregateFieldTest extends TestCase
{
    const FIELDNAME = 'test_aggregate_field';

    public function testCreate()
    {
        $aggregate_field = AggregateField::create(
            self::FIELDNAME,
            array(
                AggregateField::OPTION_MODULES => array('\\Dat0r\\Tests\\Runtime\\Module\\AggregateModule')
            )
        );
        $this->assertEquals($aggregate_field->getName(), self::FIELDNAME);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $options = array_merge(
            array(
                AggregateField::OPTION_MODULES => array('\\Dat0r\\Tests\\Runtime\\Module\\AggregateModule')
            ),
            $options
        );
        $aggregate_field = AggregateField::create(self::FIELDNAME, $options);
        $this->assertEquals($aggregate_field->getName(), self::FIELDNAME);

        $this->assertEquals($aggregate_field->getName(), self::FIELDNAME);
        $this->assertFalse($aggregate_field->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue) {
            $this->assertTrue($aggregate_field->hasOption($optName));
            $this->assertEquals($aggregate_field->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getAggregateFixture
     */
    public function testCreateValueHolder(array $aggregate_data)
    {
        $aggregate_field = AggregateField::create(
            self::FIELDNAME,
            array(
                AggregateField::OPTION_MODULES => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\AggregateModule')
            )
        );
        $value_holder = $aggregate_field->createValueHolder();
        $this->assertInstanceOf('Dat0r\\Runtime\\ValueHolder\\Type\\AggregateValueHolder', $value_holder);
        $value_holder->setValue($aggregate_data);
        $document = $value_holder->getValue()->getFirst();
        $this->assertInstanceOf('\\Dat0r\\Tests\\Runtime\\Document\\Fixtures\\DocumentTestProxy', $document);

        foreach ($aggregate_data[0] as $fieldname => $value) {
            if ($fieldname === '@type') {
                $this->assertEquals($value, $document->getModule()->getDocumentType());
            } else {
                $this->assertEquals($value, $document->getValue($fieldname));
            }
        }
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
    public static function getAggregateFixture()
    {
        // @todo generate random (utf-8) text
        $fixtures = array();

        $fixtures[] = array(
            array(
                array(
                    'title' => 'This is a paragraph test title.',
                    'content' => 'And this is some paragraph test content.',
                    '@type' => '\\Dat0r\\Tests\\Runtime\\Document\\Fixtures\\DocumentTestProxy'
                )
            )
        );

        return $fixtures;
    }
}
