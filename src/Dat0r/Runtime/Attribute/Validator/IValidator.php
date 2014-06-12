<?php

namespace Dat0r\Runtime\Attribute\Validator;

use Dat0r\Runtime\Attribute\Validator\Rule\RuleList;
use Dat0r\Runtime\Attribute\Validator\Result\IResult;

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
