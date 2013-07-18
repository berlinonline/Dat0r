<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\Generic;

class FieldDefinitionSet extends Generic\ObjectSet
{
    public static function create(array $items = array())
    {
        $item_implementor = sprintf('\\%s\\FieldDefinition', __NAMESPACE__);

        return parent::create(array(
            self::ITEM_IMPLEMENTOR => $item_implementor,
            self::ITEMS_KEY_FIELD => 'name',
            self::ITEMS => $items
        ));
    }
}
