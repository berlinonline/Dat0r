<?php

namespace Dat0r\Core\ValueHolder;

use Dat0r\Core\Error;
use Dat0r\Core\Field\IField;
use Dat0r\Core\Field\EmailField;

class EmailValueHolder extends TextValueHolder
{
    protected function __construct(IField $field, $value = null)
    {
        if (!($field instanceof EmailField)) {
            throw new Error\BadValueException(
                "Only instances of EmailField my be associated with EmailValueHolder."
            );
        }

        parent::__construct($field, $value);
    }
}
