<?php

namespace Dat0r\Tests\Runtime\Attribute;

use Dat0r\Tests\TestCase;
use Dat0r\Runtime\Attribute\AttributeValuePath;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;

class AttributeValuePathTest extends TestCase
{
    /**
     * @dataProvider articleValuePathProvider
     */
    public function testFoo($article, $value_path, $expected_value)
    {
        $value = AttributeValuePath::getAttributeValueByPath($article, $value_path);

        $this->assertEquals($expected_value, $value);
        $this->assertEquals(true, true);
    }

    public function articleValuePathProvider()
    {
        $article_type = new ArticleType();
        $headline = 'test it';
        $content = 'most sophisticated cmf ever being tested here!';
        $paragraph_title = 'this is an awesome paragraph';
        $paragraph_content = 'and even more awesome content ...';
        $category_title = 'enterprise software';
        $category_description = 'it-solutions built for medium and larger businesses';

        $article = $article_type->createDocument(
            array(
                'headline' => $headline,
                'content' => $content,
                'content_objects' => array(
                    array(
                        '@type' => '\\Dat0r\\Tests\\Runtime\\Fixtures\\Paragraph',
                        'title' => $paragraph_title,
                        'content' => $paragraph_content
                    )
                ),
                'references' => array(
                    array(
                        '@type' => '\\Dat0r\\Tests\\Runtime\\Fixtures\\Category',
                        'title' => $category_title,
                        'description' => $category_description
                    )
                )
            )
        );

        return array(
            array($article, 'headline', $headline),
            array($article, 'content_objects.paragraph[0].content', $paragraph_content),
            array($article, 'references.category[0].title', $category_title),
            array($article, 'content_objects.*[0].title', $paragraph_title),
            array($article, 'references.*[0].description', $category_description)
        );
    }
}
