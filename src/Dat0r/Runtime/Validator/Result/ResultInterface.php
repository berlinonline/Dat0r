<?php

namespace Dat0r\Runtime\Validator\Result;

use Dat0r\Runtime\Validator\Rule\RuleInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Common\Object;

interface ResultInterface
{
    /**
     * @return Object
     */
    public function getSubject();

    /**
     * @return RuleList
     */
    public function getViolatedRules();

    public function getSanitizedValue();

    public function getInputValue();

    public function getSeverity();

    public function addViolatedRule(RuleInterface $rule);
}
