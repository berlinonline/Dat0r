<?php

namespace Dat0r\Tests\Core\Field;

use Dat0r\Tests\Core\BaseTest;
use Dat0r\Tests\Core\Document\Fixtures\DocumentTestProxy;
use Dat0r\Tests\Core\Module\Fixtures\RootModule;

use Dat0r\Type\Collection\ArrayList;
use Dat0r\Runtime\Document\DocumentList;

class DocumentListTest extends BaseTest
{
    public function testCreateCollection()
    {
        $collection = new DocumentList();

        $this->assertInstanceOf('Dat0r\Runtime\Document\DocumentList', $collection);
    }

    public function testAddDocumentToEmptyCollection()
    {
        $module = RootModule::getInstance();
        $collection = new DocumentList();

        $test_document = $module->createDocument();
        $collection->addItem($test_document);

        $first_document = $collection->getFirst();
        $this->assertEquals($test_document, $first_document);
    }

    public function testAddDocumentToNonEmptyCollection()
    {
        $module = RootModule::getInstance();
        $collection = new DocumentList(
            array($module->createDocument())
        );

        $test_document = $module->createDocument();
        $collection->addItem($test_document);

        $second_document = $collection[0];
        $this->assertEquals($test_document, $second_document);
    }
}
