<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Document\Transform\Fixtures\TestTransformer;
use Dat0r\Tests\Runtime\Document\Transform\Fixtures\EmbedSpecifications;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;
use Dat0r\Tests\Runtime\Fixtures\Article;

class TransformerTest extends TestCase
{
    public function testCreate()
    {
        $transformer = new TestTransformer();

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\ITransformer', $transformer);
        $this->assertInstanceOf('\\Params\\Parameters', $transformer->getOptions());
        $this->assertEquals('bar', $transformer->getOption('foo'));
    }

    /**
     * @dataProvider provideTestDocument
     */
    public function testTransform(Article $document)
    {
        $transformer = new TestTransformer();
        $spec_container = new EmbedSpecifications();
        $transformed_data = $transformer->transform($document, $spec_container);

        $this->assertEquals($document->getValue('headline'), $transformed_data['title']);
        $this->assertEquals($document->getValue('author'), $transformed_data['author']);
    }

    public function provideTestDocument()
    {
        $type = new ArticleType();
        $test_document = $type->createDocument(
            array(
                'headline' => 'This is incredible stuff!',
                'author' => 'Thorsten Schmitt-Rink',
                'email' => 'thorsten.schmitt-rink@example.com',
                'content' => 'This is some kind of very valueable and incredible content.',
                'enabled' => true,
                'click_count' => 23,
                'images' => array(5, 23, 42),
                'keywords' => array('incredible', 'valueable'),
                'meta' => array('state' => 'edit'),
                'paragraphs' => array(
                    array(
                        'title' => 'This is an amazing paragraph',
                        'content' => 'Bob! This is just in incredible!',
                        '@type' => '\\Dat0r\\Tests\\Runtime\\Fixtures\\Paragraph'
                    )
                )
            )
        );

        return array(array($test_document));
    }
}
