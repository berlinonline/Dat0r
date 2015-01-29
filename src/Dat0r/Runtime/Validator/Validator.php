<?php

namespace Dat0r\Runtime\Validator;

use Dat0r\Common\Object;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Result\Result;
use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Rule\RuleList;

class Validator extends Object implements ValidatorInterface
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
        $result->setInputValue($value);

        $success = true;
        foreach ($this->rules as $rule) {
            if ($rule->apply($value)) {
                $value = $rule->getSanitizedValue();
            } else {
                $success = false;
                $result->addViolatedRule($rule);
                if ($result->getSeverity() === IncidentInterface::CRITICAL) {
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
