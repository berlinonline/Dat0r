<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Document\Fixtures\DocumentTestProxy;
use Dat0r\Tests\Runtime\Type\Fixtures\ArticleType;

use Dat0r\Common\Collection\ArrayList;
use Dat0r\Runtime\Document\DocumentList;

class DocumentListTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = new DocumentList();

        $this->assertInstanceOf('Dat0r\Runtime\Document\DocumentList', $collection);
    }

    public function testAddDocumentToEmptyCollection()
    {
        $type = ArticleType::getInstance();
        $collection = new DocumentList();

        $test_document = $type->createDocument();
        $collection->addItem($test_document);

        $first_document = $collection->getFirst();
        $this->assertEquals($test_document, $first_document);
    }

    public function testAddDocumentToNonEmptyCollection()
    {
        $type = ArticleType::getInstance();
        $collection = new DocumentList(
            array($type->createDocument())
        );

        $test_document = $type->createDocument();
        $collection->addItem($test_document);

        $second_document = $collection[0];
        $this->assertEquals($test_document, $second_document);
    }
}
