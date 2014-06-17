<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Document\Fixtures\DocumentTestProxy;
use Dat0r\Tests\Runtime\Fixtures\ArticleType;

use Dat0r\Common\Collection\ArrayList;
use Dat0r\Runtime\Document\DocumentList;

class DocumentTest extends TestCase
{
    public function testCreateDocument()
    {
        $type = new ArticleType();
        $document = $type->createDocument(array(
            'headline' => 'hello world!'
        ));

        $this->assertTrue($document->isValid());
        $this->assertEquals('hello world!', $document->getValue('headline'));
    }

    public function testInvalidValue()
    {
        $type = new ArticleType();
        $document = $type->createDocument(array(
            'headline' => 'hel'
        ));

        $this->assertFalse($document->isValid());
        $this->assertEquals(null, $document->getValue('headline'));
    }
}
