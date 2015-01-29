<?php

namespace Dat0r\Runtime\Validator\Result;

use Dat0r\Common\Collection\TypedMap;
use Dat0r\Common\Collection\UniqueCollectionInterface;
use Dat0r\Runtime\Validator\Result\IncidentInterface;

class ResultMap extends TypedMap implements UniqueCollectionInterface
{
    public function worstSeverity()
    {
        $severity = IncidentInterface::SUCCESS;
        foreach ($this->items as $result) {
            $severity = max($severity, $result->getSeverity());
        }

        return $severity;
    }

    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Validator\\Result\\ResultInterface';
    }
}
