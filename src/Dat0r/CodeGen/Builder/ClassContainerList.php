<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r;

class ClassContainerList extends Dat0r\ArrayList
{
    public static function create(array $items = array())
    {
        return parent::create(
            array(
                self::KEY_ITEM_IMPLEMENTOR => sprintf('\\%s\\ClassContainer', __NAMESPACE__),
                self::KEY_ITEMS => $items
            )
        );
    }
}
