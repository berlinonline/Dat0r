<?php

namespace Dat0r\Runtime\Validator;

use Dat0r\Runtime\Validator\Result\ResultInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Entity\EntityInterface;

interface ValidatorInterface
{
    /**
     * Validates the value by delegating this to validation rules.
     *
     * @return ResultInterface
     */
    public function validate($value, EntityInterface $entity = null);

    /**
     * @return RuleList
     */
    public function getRules();

    /**
     * @return string
     */
    public function getName();
}
