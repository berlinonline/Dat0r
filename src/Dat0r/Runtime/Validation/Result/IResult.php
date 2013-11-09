<?php

namespace Dat0r\Runtime\Validation\Result;

use Dat0r\Runtime\Validation\Rule\IRule;
use Dat0r\Runtime\Validation\Rule\RuleList;
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

    public function getSeverity();

    public function addViolatedRule(IRule $rule);
}
