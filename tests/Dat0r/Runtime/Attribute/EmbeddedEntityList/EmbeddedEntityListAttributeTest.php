<?php

namespace Dat0r\Tests\Runtime\Attribute\Type;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListValueHolder;
use Dat0r\Tests\Runtime\Fixtures\ParagraphType;
use Dat0r\Tests\Runtime\Fixtures\Paragraph;
use Dat0r\Tests\Runtime\Fixtures\WorkflowTicketType;

class EmbeddedEntityListAttributeTest extends TestCase
{
    const FIELDNAME = 'test_embed_attribute';

    public function testCreate()
    {
        $embed_attribute = new EmbeddedEntityListAttribute(
            self::FIELDNAME,
            array(
                EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => array(ParagraphType::CLASS)
            )
        );
        $this->assertEquals($embed_attribute->getName(), self::FIELDNAME);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $options = array_merge(
            array(
                EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => array(ParagraphType::CLASS)
            ),
            $options
        );

        $embed_attribute = new EmbeddedEntityListAttribute(self::FIELDNAME, $options);
        $this->assertEquals($embed_attribute->getName(), self::FIELDNAME);

        $this->assertEquals($embed_attribute->getName(), self::FIELDNAME);
        $this->assertFalse($embed_attribute->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue) {
            $this->assertTrue($embed_attribute->hasOption($optName));
            $this->assertEquals($embed_attribute->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getEmbedFixture
     */
    public function testCreateValue(array $embed_data)
    {
        $embed_attribute = new EmbeddedEntityListAttribute(
            self::FIELDNAME,
            array(
                EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => array(ParagraphType::CLASS)
            )
        );

        $value = $embed_attribute->createValue();
        $this->assertInstanceOf(EmbeddedEntityListValueHolder::CLASS, $value);

        $value->set($embed_data);
        $entity = $value->get()->getFirst();
        $this->assertInstanceOf(Paragraph::CLASS, $entity);

        foreach ($embed_data[0] as $attribute_name => $value) {
            if ($attribute_name === '@type') {
                $this->assertEquals($value, $entity->getType()->getEntityType());
            } else {
                $this->assertEquals($value, $entity->getValue($attribute_name));
            }
        }
    }

    public function testGetEmbedByPrefix()
    {
        $embed_attribute = new EmbeddedEntityListAttribute(
            self::FIELDNAME,
            array(
                EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => array(WorkflowTicketType::CLASS)
            )
        );
        $workflow_ticket_type = $embed_attribute->getEmbedByPrefix('workflow_ticket');
        $this->assertInstanceOf(WorkflowTicketType::CLASS, $workflow_ticket_type);
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
    public static function getEmbedFixture()
    {
        // @todo generate random (utf-8) text
        $fixtures = array();

        $fixtures[] = array(
            array(
                array(
                    'title' => 'This is a paragraph test title.',
                    'content' => 'And this is some paragraph test content.',
                    '@type' => Paragraph::CLASS
                )
            )
        );

        return $fixtures;
    }
}
