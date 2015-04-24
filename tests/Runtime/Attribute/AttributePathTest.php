<?php

namespace Dat0r\Tests\Runtime\Attribute;

use Dat0r\Runtime\Attribute\AttributePath;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;
use Dat0r\Tests\Runtime\Fixtures\ParagraphType;
use Dat0r\Tests\TestCase;

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
        return [
            [ 'content_objects.paragraph.title', 'title' ],
            [ 'headline', 'headline' ]
        ];
    }

    public function attributePathProvider()
    {
        $article_type = new ArticleType();
        $headline_attribute = $article_type->getAttribute('headline');

        $content_objects_attribute = $article_type->getAttribute('content_objects');
        $paragraph_type = $content_objects_attribute->getEmbedTypeByPrefix('paragraph');
        $title_attribute = $paragraph_type->getAttribute('title');

        $workflow_state_attribute = $article_type->getAttribute('workflow_state');
        $workflow_state_type = $workflow_state_attribute->getEmbedTypeByPrefix('workflow_state');
        $workflow_step_attribute = $workflow_state_type->getAttribute('workflow_step');

        return [
            [ $headline_attribute, 'headline' ],
            [ $title_attribute, 'content_objects.paragraph.title' ],
            [ $workflow_step_attribute, 'workflow_state.workflow_state.workflow_step' ]
        ];
    }
}
