<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Runtime\Entity\Entity;

class Category extends Entity
{
    public function getIdentifier()
    {
        return $this->getValue('title');
    }
}