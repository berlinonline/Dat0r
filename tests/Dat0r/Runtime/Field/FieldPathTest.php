<?php

namespace Dat0r\Tests\Runtime\Field;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Field\FieldPath;
use Dat0r\Tests\Runtime\Module\Fixtures\ArticleModule;
use Dat0r\Tests\Runtime\Module\Fixtures\ParagraphModule;

class FieldPathTest extends TestCase
{
    /**
     * @dataProvider fieldByPathProvider
     */
    public function testGetFieldByPath($field_path, $expected_name)
    {
        $module = ArticleModule::getInstance();
        $field = FieldPath::getFieldByPath($module, $field_path);
        $this->assertEquals($expected_name, $field->getName());
    }

    /**
     * @dataProvider fieldPathProvider
     */
    public function testGetFieldPath($field, $expected_path)
    {
        $module = ArticleModule::getInstance();
        $field_path = FieldPath::getFieldPath($field);

        $this->assertEquals($expected_path, $field_path);
    }

    public function fieldByPathProvider()
    {
        return array(
            array('paragraph.paragraph.title', 'title'),
            array('references.category.description', 'description'),
            array('headline', 'headline')
        );
    }

    public function fieldPathProvider()
    {
        $article_module = ArticleModule::getInstance();
        $headline_field = $article_module->getField('headline');

        $paragraph_field = $article_module->getField('paragraph');
        $paragraph_module = $paragraph_field->getAggregateModuleByPrefix('paragraph');
        $title_field = $paragraph_module->getField('title');

        $references_field = $article_module->getField('references');
        $category_module = $references_field->getReferenceModuleByPrefix('category');
        $description_field = $category_module->getField('description');

        return array(
            array($headline_field, 'headline'),
            array($title_field, 'paragraph.paragraph.title'),
            array($description_field, 'references.category.description')
        );
    }
}
