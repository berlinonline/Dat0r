<?php

namespace Dat0r\Tests\Fixtures;

use Dat0r;

class TestObjectList extends Dat0r\ObjectList
{
    public static function create(array $items = array())
    {
        $item_implementor = sprintf('\\%s\\TestObjectList', __NAMESPACE__);

        return parent::create(array(
            self::ITEM_IMPLEMENTOR => $item_implementor,
            self::ITEMS => $items
        ));
    }
}
