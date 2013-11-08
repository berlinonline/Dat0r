<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Field\EmailField;

class EmailValueHolder extends TextValueHolder
{
    protected function __construct(IField $field, $value = null)
    {
        if (!($field instanceof EmailField)) {
            throw new BadValueException(
                "Only instances of EmailField my be associated with EmailValueHolder."
            );
        }

        parent::__construct($field, $value);
    }
}
