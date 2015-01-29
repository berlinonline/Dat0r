<?php

namespace Dat0r\Runtime\Validator;

use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Result\ResultInterface;

interface ValidatorInterface
{
    /**
     * Validates the value by delegating this to validation rules.
     *
     * @return ResultInterface
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
