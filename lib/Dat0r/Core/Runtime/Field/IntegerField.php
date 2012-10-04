<?php

namespace Dat0r\Core\Runtime\Field;

/**
 * Concreate implementation of the Field base class.
 * Stuff in here is dedicated to handling integer values.
 */
class IntegerField extends Field
{
    /**
     * Returns the IValueHolder implementation to use when aggregating (value)data for this field.
     *
     * @return string Fully qualified name of an IValueHolder implementation.
     */
    protected function getValueHolderImplementor()
    {
        return 'Dat0r\Core\Runtime\ValueHolder\IntegerValueHolder';
    }

    /**
     * Returns the IValidator implementation to use when validating values for this field.
     *
     * @return string Fully qualified name of an IValidator implementation.
     */
    protected function getValidationImplementor()
    {
        return 'Dat0r\Core\Runtime\Validator\IntegerValidator';
    }
}