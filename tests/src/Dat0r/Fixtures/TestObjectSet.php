<?php

namespace Dat0r\Tests\Fixtures;

use Dat0r;

class TestObjectSet extends Dat0r\Set
{
    const ITEM_IMPLEMENTOR = '\\Dat0r\\Tests\\Fixtures\\TestObject';

    public static function create(array $items = array())
    {
        return parent::create(
            array(
                self::KEY_ITEM_IMPLEMENTOR => self::ITEM_IMPLEMENTOR,
                self::KEY_ITEMS => $items
            )
        );
    }
}
