<?php

namespace Dat0r\Runtime\Validator\Result;

use Dat0r\Runtime\Validator\Rule\IRule;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Common\Object;

interface IResult
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

    public function addViolatedRule(IRule $rule);
}
