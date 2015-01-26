<?php

namespace Dat0r\Runtime\Validator\Rule;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\UniqueCollectionInterface;

class RuleList extends TypedList implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Validator\\Rule\\IRule';
    }
}
