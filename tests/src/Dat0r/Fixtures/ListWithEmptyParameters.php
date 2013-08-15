<?php

namespace Dat0r\Tests\Fixtures;

use Dat0r;

class ListWithEmptyParameters extends Dat0r\ArrayList
{
    public static function create(array $items = array())
    {
        $parameters = array();

        return parent::create($parameters);
    }
}
