<?php

namespace Dat0r\Core\Validator;

class EmailValidator extends TextValidator
{
    public function validate($value)
    {
        if (!parent::validate($value)) {
            return false;
        }

        if (!empty($value)) {
            return filter_var((string)$value, FILTER_VALIDATE_EMAIL);
        }

        return true;
    }
}
