<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Runtime\DocumentType;

class InvalidType extends DocumentType
{
    public function __construct()
    {
        parent::__construct('InvalidType');
    }

    protected function getDocumentImplementor()
    {
        return 'NonExistantDocumentClass';
    }
}
