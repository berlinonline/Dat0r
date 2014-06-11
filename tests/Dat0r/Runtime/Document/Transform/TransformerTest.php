<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Document\Transform\Fixtures\TestTransformer;
use Dat0r\Tests\Runtime\Document\Transform\Fixtures\EmbedSpecifications;
use Dat0r\Tests\Runtime\Module\Fixtures\ArticleModule;
use Dat0r\Tests\Runtime\Document\Fixtures\DocumentTestProxy;

class TransformerTest extends TestCase
{
    public function testCreate()
    {
        $transformer = TestTransformer::create();

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\ITransformer', $transformer);
        $this->assertInstanceOf('\\Dat0r\\Common\\Options', $transformer->getOptions());
        $this->assertEquals('bar', $transformer->getOption('foo'));
    }

    /**
     * @dataProvider provideTestDocument
     */
    public function testTransform(DocumentTestProxy $document)
    {
        $transformer = TestTransformer::create();
        $spec_container = EmbedSpecifications::create();
        $transformed_data = $transformer->transform($document, $spec_container);

        $this->assertEquals($document->getValue('headline'), $transformed_data['title']);
        $this->assertEquals($document->getValue('author'), $transformed_data['author']);
    }

    public function provideTestDocument()
    {
        $module = ArticleModule::getInstance();
        $test_document = $module->createDocument(
            array(
                'headline' => 'This is incredible stuff!',
                'author' => 'Thorsten Schmitt-Rink',
                'email' => 'thorsten.schmitt-rink@example.com',
                'content' => 'This is some kind of very valueable and incredible content.',
                'enabled' => true,
                'clickCount' => 23,
                'images' => array(5, 23, 42),
                'keywords' => array('incredible', 'valueable'),
                'meta' => array('state' => 'edit'),
                'paragraphs' => array(
                    array(
                        'title' => 'This is an amazing paragraph',
                        'content' => 'Bob! This is just in incredible!',
                        '@type' => '\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\ParagraphModule'
                    )
                )
            )
        );

        return array(array($test_document));
    }
}
