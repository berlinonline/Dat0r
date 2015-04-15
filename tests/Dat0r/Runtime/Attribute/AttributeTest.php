<?php

namespace Dat0r\Tests\Runtime\Attribute;

use Dat0r\Tests\Runtime\Fixtures\ArticleType;
use Dat0r\Tests\TestCase;

class AttributeTest extends TestCase
{
    /**
     * @dataProvider attributePathProvider
     */
    public function testGetPath($attribute, $expected_path)
    {
        $this->assertEquals($expected_path, $attribute->getPath());
    }

    /**
     * @dataProvider attributeRootTypeProvider
     */
    public function testGetRootType($attribute, $entity_type)
    {
        $this->assertEquals($entity_type->getName(), $attribute->getRootType()->getName());
    }

    public function attributePathProvider()
    {
        $article_type = new ArticleType();
        $headline_attribute = $article_type->getAttribute('headline');

        $content_objects_attribute = $article_type->getAttribute('content_objects');
        $paragraph_type = $content_objects_attribute->getEmbedTypeByPrefix('paragraph');
        $title_attribute = $paragraph_type->getAttribute('title');

        $workflow_ticket_attribute = $article_type->getAttribute('workflow_ticket');
        $workflow_ticket_type = $workflow_ticket_attribute->getEmbedTypeByPrefix('workflow_ticket');
        $workflow_step_attribute = $workflow_ticket_type->getAttribute('workflow_step');

        return array(
            array($headline_attribute, 'headline'),
            array($title_attribute, 'content_objects.paragraph.title'),
            array($workflow_step_attribute, 'workflow_ticket.workflow_ticket.workflow_step')
        );
    }

    public function attributeRootTypeProvider()
    {
        $article_type = new ArticleType();
        $headline_attribute = $article_type->getAttribute('headline');

        $content_objects_attribute = $article_type->getAttribute('content_objects');
        $paragraph_type = $content_objects_attribute->getEmbedTypeByPrefix('paragraph');
        $title_attribute = $paragraph_type->getAttribute('title');

        $workflow_ticket_attribute = $article_type->getAttribute('workflow_ticket');
        $workflow_ticket_type = $workflow_ticket_attribute->getEmbedTypeByPrefix('workflow_ticket');
        $workflow_step_attribute = $workflow_ticket_type->getAttribute('workflow_step');

        return array(
            array($headline_attribute, $article_type),
            array($title_attribute, $article_type),
            array($workflow_step_attribute, $article_type)
        );
    }
}
