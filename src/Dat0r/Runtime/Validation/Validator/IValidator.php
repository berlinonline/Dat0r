<?php

namespace Dat0r\Runtime\Validation\Validator;

use Dat0r\Runtime\Validation\Rule\RuleList;
use Dat0r\Runtime\Validation\Result\IResult;

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
