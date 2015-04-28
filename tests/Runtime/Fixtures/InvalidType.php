<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Runtime\EntityType;

class InvalidType extends EntityType
{
    public function __construct()
    {
        parent::__construct('InvalidType');
    }

    public function getEntityImplementor()
    {
        return 'NonExistantEntityClass';
    }
}
