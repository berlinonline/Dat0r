<?php

namespace Dat0r\Tests\Runtime\Type\Fixtures;

use Dat0r\Runtime\Type\AggregateRoot;

class InvalidType extends AggregateRoot
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
