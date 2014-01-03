<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Document\Transform\Fixtures\TestTransformer;
use Dat0r\Tests\Runtime\Module\Fixtures\RootModule;
use Dat0r\Tests\Runtime\Document\Fixtures\DocumentTestProxy;

class TransformerTest extends TestCase
{
    public function testCreate()
    {
        $transformer = TestTransformer::create();

        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\ITransformer', $transformer);

        $this->assertInstanceOf('\\Dat0r\\Common\\Options', $transformer->getOptions());
        $this->assertEquals('bar', $transformer->getOptions()->get('foo', 'default'));
        $this->assertInstanceOf('\\Dat0r\\Runtime\\Document\\Transform\\IFieldSpecifications', $transformer->getFieldSpecifications());
        $this->assertEquals('embed', $transformer->getFieldSpecifications()->getName());
    }

    /**
     * @dataProvider provideTestDocument
     */
    public function testTransform(DocumentTestProxy $document)
    {
        $transformer = TestTransformer::create();
        $transformed_data = $transformer->transform($document);

        $this->assertEquals($document->getValue('headline'), $transformed_data['title']);
        $this->assertEquals($document->getValue('author'), $transformed_data['author']);
    }

    public function provideTestDocument()
    {
        $module = RootModule::getInstance();
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
                        'content' => 'Bob! This is just in incredible! If you read one line now you can read the next three right afterwards for free.',
                        '@type' => '\\Dat0r\\Tests\\Runtime\\Module\\Fixtures\\AggregateModule'
                    )
                )
            )
        );

        return array(array($test_document));
    }
}
