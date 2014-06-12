<?php

namespace Dat0r\Runtime\Attribute\Validator\Rule;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\IUniqueCollection;

class RuleList extends TypedList implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Attribute\\Validator\\Rule\\IRule';
    }
}
