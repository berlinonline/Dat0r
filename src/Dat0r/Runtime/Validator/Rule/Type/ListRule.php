<?php

namespace Dat0r\Runtime\Validator\Rule\Type;

use Dat0r\Runtime\Attribute\ListAttribute;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;
use Traversable;

class ListRule extends Rule
{
    protected function execute($value)
    {
        $cast_to_array = $this->toBoolean($this->getOption(ListAttribute::OPTION_CAST_TO_ARRAY, true));
        if ((!$cast_to_array && !is_array($value)) || (!$cast_to_array && !$value instanceof Traversable)) {
            $this->throwError('not_an_array');
            return false;
        }

        $success = true;

        $casted = [];
        if (is_array($value)) {
            $casted = $value;
        } elseif ($value instanceof Traversable) {
            foreach ($value as $key => $item) {
                $casted[$key] = $item;
            }
        } else {
            $casted = [ $value ];
        }

        $count = count($casted);

        if ($this->hasOption(ListAttribute::OPTION_MIN_COUNT)) {
            $min_count = $this->getOption(ListAttribute::OPTION_MIN_COUNT, 0);
            if ($count < (int)$min_count) {
                $this->throwError('min_count', [ 'count' => $count, 'min_count' => $min_count ]);
                $success = false;
            }
        }

        if ($this->hasOption(ListAttribute::OPTION_MAX_COUNT)) {
            $max_count = $this->getOption(ListAttribute::OPTION_MAX_COUNT, 0);
            if ($count > (int)$max_count) {
                $this->throwError('max_count', [ 'count' => $count, 'max_count' => $max_count ]);
                $success = false;
            }
        }

        // export valid values
        if ($success) {
            $this->setSanitizedValue($casted);
        }

        return $success;
    }
}
