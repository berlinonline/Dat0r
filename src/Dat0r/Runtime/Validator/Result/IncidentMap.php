<?php

namespace Dat0r\Runtime\Validator\Result;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

class IncidentMap extends TypedMap implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Validator\\Result\\IIncident';
    }
}
