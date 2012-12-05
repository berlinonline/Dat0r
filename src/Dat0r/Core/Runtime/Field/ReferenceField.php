<?php

namespace Dat0r\Core\Runtime\Field;

/**
 * Concrete implementation of the Field base class.
 * Stuff in here is dedicated to handling references to documents.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class ReferenceField extends Field
{
    const OPT_MAX_REFERENCES = 'max_references';

    const OPT_REFERENCES = 'references';

    const OPT_DISPLAY_FIELD = 'display_field';

    const OPT_IDENTITY_FIELD = 'identity_field';

    /**
     * Returns the IValueHolder implementation to use when aggregating (value)data for this field.
     *
     * @return string Fully qualified name of an IValueHolder implementation.
     */
    protected function getValueHolderImplementor()
    {
        return 'Dat0r\\Core\Runtime\\ValueHolder\\ReferenceValueHolder';
    }

    /**
     * Returns the IValidator implementation to use when validating values for this field.
     *
     * @return string Fully qualified name of an IValidator implementation.
     */
    protected function getValidationImplementor()
    {
        return 'Dat0r\\Core\Runtime\\Validator\\ReferenceValidator';
    }
}
