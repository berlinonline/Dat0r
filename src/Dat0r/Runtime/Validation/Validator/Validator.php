<?php

namespace Dat0r\Runtime\Validation\Validator;

use Dat0r\Common\Object;
use Dat0r\Runtime\Validation\Result\Result;
use Dat0r\Runtime\Validation\Result\IIncident;
use Dat0r\Runtime\Validation\Rule\RuleList;
use Dat0r\Runtime\Validation\Rule\Rule;

class Validator extends Object implements IValidator
{
    protected $name;

    protected $rules;

    public function __construct($name, RuleList $rules)
    {
        $this->name = $name;
        $this->rules = $rules;
    }

    public function validate($value)
    {
        $result = new Result($this);

        $success = true;
        foreach ($this->rules as $rule) {
            if ($rule->apply($value)) {
                $value = $rule->getSanitizedValue();
            } else {
                $success = false;
                $result->addViolatedRule($rule);
                if ($result->getSeverity() === IIncident::CRITICAL) {
                    // abort validation process for critical errors
                    break;
                }
            }
        }

        if ($success) {
            $result->setSanitizedValue($value);
        }

        return $result;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRules()
    {
        return $this->rules;
    }
}
