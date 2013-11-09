<?php

namespace Dat0r\Tests\Runtime\Document;

use Dat0r\Tests\TestCase;
use Dat0r\Tests\Runtime\Document\Fixtures\DocumentTestProxy;
use Dat0r\Tests\Runtime\Module\Fixtures\RootModule;

use Dat0r\Common\Collection\ArrayList;
use Dat0r\Runtime\Document\DocumentList;

class DocumentTest extends TestCase
{
    public function testCreateDocument()
    {
        $module = RootModule::getInstance();
        $document = $module->createDocument(array(
            'headline' => 'hell world!'
        ));
    }
}
