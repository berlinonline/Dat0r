<?php

namespace Dat0r\Runtime\Validation\Rule;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\IUniqueCollection;

class RuleList extends TypedList implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Validation\\Rule\\IRule';
    }
}
