<?php

namespace Dat0r\Runtime\Validator\Result;

use Dat0r\Common\Object;
use Dat0r\Runtime\Validator\Rule\RuleInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\ValidatorInterface;

class Result extends Object implements ResultInterface
{
    protected $subject;

    protected $violated_rules;

    protected $severity;

    protected $input_value;

    protected $sanitized_value;

    public function __construct(ValidatorInterface $subject)
    {
        $this->subject = $subject;
        $this->severity = IncidentInterface::SUCCESS;
        $this->sanitized_value = null;
        $this->violated_rules = new RuleList();
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getViolatedRules()
    {
        return $this->violated_rules;
    }

    public function getInputValue()
    {
        return $this->input_value;
    }

    public function setInputValue($input_value)
    {
        return $this->input_value = $input_value;
    }

    public function getSanitizedValue()
    {
        return $this->sanitized_value;
    }

    public function setSanitizedValue($sanitized_value)
    {
        return $this->sanitized_value = $sanitized_value;
    }

    public function getSeverity()
    {
        return $this->severity;
    }

    public function addViolatedRule(RuleInterface $rule)
    {
        $this->violated_rules->addItem($rule);

        foreach ($rule->getIncidents() as $incident) {
            if ($incident->getSeverity() > $this->severity) {
                $this->severity = $incident->getSeverity();
            }
        }
    }
}
