<?php

namespace Dat0r\Runtime\Validator;

use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Result\IResult;

interface IValidator
{
    /**
     * @return IResult
     */
    public function validate($value);

    /**
     * @return RuleList
     */
    public function getRules();

    /**
     * @return string
     */
    public function getName();
}
