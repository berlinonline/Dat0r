<?php

namespace Dat0r\Runtime\Attribute;

use Dat0r\Common\Error\InvalidConfigException;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Type\ListRule;

/**
 * Attribute that has a ListValueHolder (usually an array internally).
 */
abstract class ListAttribute extends Attribute
{
    const OPTION_MAX_COUNT = 'max_count';
    const OPTION_MIN_COUNT = 'min_count';
    const OPTION_CAST_TO_ARRAY = 'cast_to_array';

    /**
     * Returns an attribute's null value.
     *
     * @return mixed value to be used/interpreted as null (not set)
     */
    public function getNullValue()
    {
        return [];
    }

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new ListRule('valid-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
