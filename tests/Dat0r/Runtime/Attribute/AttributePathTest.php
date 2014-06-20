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
            array('references.category.description', 'description'),
            array('headline', 'headline')
        );
    }

    public function attributePathProvider()
    {
        $article_type = new ArticleType();
        $headline_attribute = $article_type->getAttribute('headline');

        $content_objects_attribute = $article_type->getAttribute('content_objects');
        $paragraph_type = $content_objects_attribute->getAggregateByPrefix('paragraph');
        $title_attribute = $paragraph_type->getAttribute('title');

        $references_attribute = $article_type->getAttribute('references');
        $category_type = $references_attribute->getReferenceByPrefix('category');
        $description_attribute = $category_type->getAttribute('description');

        return array(
            array($headline_attribute, 'headline'),
            array($title_attribute, 'content_objects.paragraph.title'),
            array($description_attribute, 'references.category.description')
        );
    }
}
