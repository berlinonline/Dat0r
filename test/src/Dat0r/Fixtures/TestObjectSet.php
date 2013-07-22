<?php

namespace Dat0r\Tests\Fixtures;

use Dat0r;

class TestObjectSet extends Dat0r\Set
{
    public static function create(array $items = array())
    {
        $item_implementor = sprintf('\\%s\\TestObject', __NAMESPACE__);

        return parent::create(
            array(
                self::ITEM_IMPLEMENTOR => $item_implementor,
                self::ITEMS_KEY_FIELD => 'property_one',
                self::ITEMS => $items
            )
        );
    }
}
