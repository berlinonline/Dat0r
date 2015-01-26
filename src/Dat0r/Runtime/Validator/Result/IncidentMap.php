<?php

namespace Dat0r\Runtime\Validator\Result;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\UniqueCollectionInterface;

class IncidentMap extends TypedMap implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Validator\\Result\\IncidentInterface';
    }
}
