<?php

namespace Dat0r\Runtime\Attribute\Validator\Result;

use Dat0r\Runtime\Attribute\Validator\Result\IIncident;
use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\IUniqueCollection;

class ResultMap extends TypedMap implements IUniqueCollection
{
    public function worstSeverity()
    {
        $severity = IIncident::SUCCESS;
        foreach ($this->items as $result) {
            $severity = max($severity, $result->getSeverity());
        }

        return $severity;
    }

    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Attribute\\Validator\\Result\\IResult';
    }
}
