<?php

namespace Dat0r\Core\Runtime\Validator;

use Dat0r\Core\Runtime\Document\IDocument;
use Dat0r\Core\Runtime\Document\DocumentCollection;
use Dat0r\Core\Runtime\Field\ReferenceField;

/**
 * Default implementation for validators that validate text.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class ReferenceValidator extends Validator
{
    /**
     * Validates a given value thereby considering the state of the field
     * that a specific validator instance is related to.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function validate($value)
    {
        if (! ($value instanceof DocumentCollection))
        {
            return FALSE;
        }

        $referenceMap = array();
        foreach ($this->getField()->getOption(ReferenceField::OPT_REFERENCES) as $reference)
        {
            $referenceMap[$reference[ReferenceField::OPT_MODULE]] = $reference[ReferenceField::OPT_IDENTITY_FIELD];
        }

        foreach ($value as $document)
        {
            $module = $document->getModule();
            $moduleImplementor = get_class($module);

            if (! isset($referenceMap[$moduleImplementor]))
            {
                return FALSE;
            }

            $identifierField = $referenceMap[$moduleImplementor];
            $identifier = $document->getValue($identifierField);

            if (empty($identifier))
            {
                return FALSE;
            }
        }

        return TRUE;
    }
}
