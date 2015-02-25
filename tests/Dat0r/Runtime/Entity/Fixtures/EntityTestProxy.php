<?php

namespace Dat0r\Tests\Runtime\Entity\Fixtures;

use Dat0r\Runtime\Entity\Entity;

class EntityTestProxy extends Entity
{
    public function getIdentifier()
    {
        return 'some-identifier';
    }
}
