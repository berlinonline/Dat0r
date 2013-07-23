<?php

namespace Dat0r\Tests\Fixtures;

use Dat0r;

class ListWithInvalidParameters extends Dat0r\ArrayList
{
    const INVALID_IMPLEMENTOR = 'FlexibleGraphPreference';

    public static function create(array $items = array())
    {
        return parent::create(
            array(
                self::KEY_ITEM_IMPLEMENTOR => self::INVALID_IMPLEMENTOR,
                self::KEY_ITEMS => $items
            )
        );
    }
}
