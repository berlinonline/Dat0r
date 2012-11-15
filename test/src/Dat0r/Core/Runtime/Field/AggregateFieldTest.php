<?php

namespace Dat0r\Tests\Core\Runtime\Field;
use Dat0r\Tests\Core;

use Dat0r\Core\Runtime\Field;

class AggregateFieldTest extends Core\BaseTest
{
    const FIELDNAME = 'test_aggregate_field';

    public function testCreate()
    {
        $aggregateField = Field\AggregateField::create(self::FIELDNAME, array(
            Field\AggregateField::OPT_AGGREGATE_MODULE => 'Dat0r\\Tests\\Core\\Runtime\\Module\\AggregateModule'
        ));
        $this->assertEquals($aggregateField->getName(), self::FIELDNAME);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $options = array_merge(array(
            Field\AggregateField::OPT_AGGREGATE_MODULE => 'Dat0r\\Tests\\Core\\Runtime\\Module\\AggregateModule'
        ), $options);
        $aggregateField = Field\AggregateField::create(self::FIELDNAME, $options);
        $this->assertEquals($aggregateField->getName(), self::FIELDNAME);

        $this->assertEquals($aggregateField->getName(), self::FIELDNAME);
        $this->assertFalse($aggregateField->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue)
        {
            $this->assertTrue($aggregateField->hasOption($optName));
            $this->assertEquals($aggregateField->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getAggregateFixture
     */
    public function testCreateValueHolder(array $aggregateData)
    {
        $aggregateField = Field\AggregateField::create(self::FIELDNAME, array(
            Field\AggregateField::OPT_AGGREGATE_MODULE => 'Dat0r\\Tests\\Core\\Runtime\\Module\\AggregateModule'
        ));
        $valueHolder = $aggregateField->createValueHolder($aggregateData);
        $this->assertInstanceOf('Dat0r\\Core\\Runtime\\ValueHolder\\AggregateValueHolder', $valueHolder);

        $document = $valueHolder->getValue();
        $this->assertInstanceOf('Dat0r\\Tests\\Core\\Runtime\\Document\\DocumentTestProxy', $document);
        
        foreach ($aggregateData as $fieldname => $value)
        {
            $this->assertEquals($value, $document->getValue($fieldname));
        }
    }

    /**
     * @dataProvider getAggregateFixture
     */
    public function testValidate(array $aggregateData)
    {
        $aggregateField = Field\AggregateField::create(self::FIELDNAME, array(
            Field\AggregateField::OPT_AGGREGATE_MODULE => 'Dat0r\\Tests\\Core\\Runtime\\Module\\AggregateModule'
        ));
        
        $this->assertTrue($aggregateField->validate($aggregateData));
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

        $fixtures[] = array(array(
            'title' => 'This is a paragraph test title.',
            'content' => 'And this is some paragraph test content.'
        ));

        return $fixtures;
    }
}
