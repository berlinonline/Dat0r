<?php

namespace Dat0r\Tests\Core\Field;

use Dat0r\Tests\Core;
use Dat0r\Core\Field;

class AggregateFieldTest extends Core\BaseTest
{
    const FIELDNAME = 'test_aggregate_field';

    public function testCreate()
    {
        $aggregateField = Field\AggregateField::create(
            self::FIELDNAME,
            array(
                Field\AggregateField::OPT_MODULES => array('\\Dat0r\\Tests\\Core\\Module\\AggregateModule')
            )
        );
        $this->assertEquals($aggregateField->getName(), self::FIELDNAME);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $options = array_merge(
            array(
                Field\AggregateField::OPT_MODULES => array('\\Dat0r\\Tests\\Core\\Module\\AggregateModule')
            ),
            $options
        );
        $aggregateField = Field\AggregateField::create(self::FIELDNAME, $options);
        $this->assertEquals($aggregateField->getName(), self::FIELDNAME);

        $this->assertEquals($aggregateField->getName(), self::FIELDNAME);
        $this->assertFalse($aggregateField->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue) {
            $this->assertTrue($aggregateField->hasOption($optName));
            $this->assertEquals($aggregateField->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getAggregateFixture
     */
    public function testCreateValueHolder(array $aggregateData)
    {
        $aggregateField = Field\AggregateField::create(
            self::FIELDNAME,
            array(
                Field\AggregateField::OPT_MODULES => array('\\Dat0r\\Tests\\Core\\Module\\AggregateModule')
            )
        );
        $valueHolder = $aggregateField->createValueHolder($aggregateData);
        $this->assertInstanceOf('Dat0r\\Core\\ValueHolder\\AggregateValueHolder', $valueHolder);

        $document = $valueHolder->getValue()->first();
        $this->assertInstanceOf('Dat0r\\Tests\\Core\\Document\\DocumentTestProxy', $document);

        foreach ($aggregateData[0] as $fieldname => $value) {
            if ($fieldname === 'type') {
                $this->assertEquals($value, $document->getModule()->getDocumentType());
            } else {
                $this->assertEquals($value, $document->getValue($fieldname));
            }
        }
    }

    /**
     * @dataProvider getAggregateFixture
     */
    public function testValidate(array $aggregateData)
    {
        $aggregateField = Field\AggregateField::create(
            self::FIELDNAME,
            array(
                Field\AggregateField::OPT_MODULES => array('\\Dat0r\\Tests\\Core\\Module\\AggregateModule')
            )
        );

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

        $fixtures[] = array(
            array(
                array(
                    'title' => 'This is a paragraph test title.',
                    'content' => 'And this is some paragraph test content.',
                    'type' => '\\Dat0r\\Tests\\Core\\Document\\DocumentTestProxy'
                )
            )
        );

        return $fixtures;
    }
}
