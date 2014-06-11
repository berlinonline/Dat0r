<?php

namespace Dat0r\Tests\Runtime\Attribute;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\AttributePath;
use Dat0r\Tests\Runtime\Module\Fixtures\ArticleModule;
use Dat0r\Tests\Runtime\Module\Fixtures\ParagraphModule;

class AttributePathTest extends TestCase
{
    /**
     * @dataProvider attributeByPathProvider
     */
    public function testGetAttributeByPath($attribute_path, $expected_name)
    {
        $module = ArticleModule::getInstance();
        $attribute = AttributePath::getAttributeByPath($module, $attribute_path);
        $this->assertEquals($expected_name, $attribute->getName());
    }

    /**
     * @dataProvider attributePathProvider
     */
    public function testGetAttributePath($attribute, $expected_path)
    {
        $module = ArticleModule::getInstance();
        $attribute_path = AttributePath::getAttributePath($attribute);

        $this->assertEquals($expected_path, $attribute_path);
    }

    public function attributeByPathProvider()
    {
        return array(
            array('paragraph.paragraph.title', 'title'),
            array('references.category.description', 'description'),
            array('headline', 'headline')
        );
    }

    public function attributePathProvider()
    {
        $article_module = ArticleModule::getInstance();
        $headline_attribute = $article_module->getAttribute('headline');

        $paragraph_attribute = $article_module->getAttribute('paragraph');
        $paragraph_module = $paragraph_attribute->getAggregateModuleByPrefix('paragraph');
        $title_attribute = $paragraph_module->getAttribute('title');

        $references_attribute = $article_module->getAttribute('references');
        $category_module = $references_attribute->getReferenceModuleByPrefix('category');
        $description_attribute = $category_module->getAttribute('description');

        return array(
            array($headline_attribute, 'headline'),
            array($title_attribute, 'paragraph.paragraph.title'),
            array($description_attribute, 'references.category.description')
        );
    }
}
