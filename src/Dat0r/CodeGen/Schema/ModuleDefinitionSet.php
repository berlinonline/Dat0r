<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r;

class ModuleDefinitionSet extends Dat0r\Set
{
    public static function create(array $items = array())
    {
        return parent::create(
            array(
                self::KEY_ITEM_IMPLEMENTOR => sprintf('\\%s\\ModuleDefinition', __NAMESPACE__),
                self::KEY_ITEMS => $items
            )
        );
    }
}
