<?php

namespace Dat0r\Tests\Runtime\Attribute;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\Type\AggregateCollection;

class AggregateCollectionTest extends TestCase
{
    const FIELDNAME = 'test_aggregate_attribute';

    public function testCreate()
    {
        $aggregate_attribute = new AggregateCollection(
            self::FIELDNAME,
            array(
                AggregateCollection::OPTION_MODULES => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\ParagraphModule')
            )
        );
        $this->assertEquals($aggregate_attribute->getName(), self::FIELDNAME);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $options = array_merge(
            array(
                AggregateCollection::OPTION_MODULES => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\ParagraphModule')
            ),
            $options
        );
        $aggregate_attribute = new AggregateCollection(self::FIELDNAME, $options);
        $this->assertEquals($aggregate_attribute->getName(), self::FIELDNAME);

        $this->assertEquals($aggregate_attribute->getName(), self::FIELDNAME);
        $this->assertFalse($aggregate_attribute->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue) {
            $this->assertTrue($aggregate_attribute->hasOption($optName));
            $this->assertEquals($aggregate_attribute->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getAggregateFixture
     */
    public function testCreateValueHolder(array $aggregate_data)
    {
        $aggregate_attribute = new AggregateCollection(
            self::FIELDNAME,
            array(
                AggregateCollection::OPTION_MODULES => array('\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\ParagraphModule')
            )
        );
        $value_holder = $aggregate_attribute->createValueHolder();
        $this->assertInstanceOf('Dat0r\\Runtime\\ValueHolder\\Type\\AggregateCollectionValueHolder', $value_holder);
        $value_holder->setValue($aggregate_data);
        $document = $value_holder->getValue()->getFirst();
        $this->assertInstanceOf('\\Dat0r\\Tests\\Runtime\\Document\\Fixtures\\DocumentTestProxy', $document);

        foreach ($aggregate_data[0] as $attribute_name => $value) {
            if ($attribute_name === '@type') {
                $this->assertEquals($value, $document->getModule()->getDocumentType());
            } else {
                $this->assertEquals($value, $document->getValue($attribute_name));
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
