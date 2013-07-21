<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r;

class ClassContainerList extends Dat0r\ArrayList
{
    public static function create(array $items = array())
    {
        $item_implementor = sprintf('\\%s\\ClassContainer', __NAMESPACE__);

        return parent::create(array(
            self::ITEM_IMPLEMENTOR => $item_implementor,
            self::ITEMS => $items
        ));
    }
}
