<?php

namespace Dat0r\Runtime\Validator\Rule;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\UniqueCollectionInterface;
use Dat0r\Runtime\Validator\Rule\RuleInterface;

class RuleList extends TypedList implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return RuleInterface::CLASS;
    }
}
