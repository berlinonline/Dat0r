<?php

namespace Dat0r\Tests\Runtime\Attribute;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\AttributePath;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;
use Dat0r\Tests\Runtime\Fixtures\ParagraphType;

class AttributePathTest extends TestCase
{
    /**
     * @dataProvider attributeByPathProvider
     */
    public function testGetAttributeByPath($attribute_path, $expected_name)
    {
        $type = new ArticleType();
        $attribute = AttributePath::getAttributeByPath($type, $attribute_path);
        $this->assertEquals($expected_name, $attribute->getName());
    }

    /**
     * @dataProvider attributePathProvider
     */
    public function testGetAttributePath($attribute, $expected_path)
    {
        $type = new ArticleType();
        $attribute_path = AttributePath::getAttributePath($attribute);

        $this->assertEquals($expected_path, $attribute_path);
    }

    public function attributeByPathProvider()
    {
        return array(
            array('content_objects.paragraph.title', 'title'),
            array('headline', 'headline')
        );
    }

    public function attributePathProvider()
    {
        $article_type = new ArticleType();
        $headline_attribute = $article_type->getAttribute('headline');

        $content_objects_attribute = $article_type->getAttribute('content_objects');
        $paragraph_type = $content_objects_attribute->getEmbedByPrefix('paragraph');
        $title_attribute = $paragraph_type->getAttribute('title');

        $workflow_ticket_attribute = $article_type->getAttribute('workflow_ticket');
        $workflow_ticket_type = $workflow_ticket_attribute->getEmbedByPrefix('workflow_ticket');
        $workflow_step_attribute = $workflow_ticket_type->getAttribute('workflow_step');

        return array(
            array($headline_attribute, 'headline'),
            array($title_attribute, 'content_objects.paragraph.title'),
            array($workflow_step_attribute, 'workflow_ticket.workflow_ticket.workflow_step')
        );
    }
}
