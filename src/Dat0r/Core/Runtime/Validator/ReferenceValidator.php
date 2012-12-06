<?php

namespace Dat0r\Core\Runtime\Validator;

use Dat0r\Core\Runtime\Document\IDocument;
use Dat0r\Core\Runtime\Document\DocumentSet;
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
        if (! ($value instanceof DocumentSet))
        {
            return FALSE;
        }

        // @todo dont forget to adjust when introducing multi-reference fields.
        $referencedModules = $this->getField()->getReferencedModules();
        $referencedModule = $referencedModules[0];

        $references = $this->getField()->getOption(ReferenceField::OPT_REFERENCES);
        $identityField = $references[0][ReferenceField::OPT_IDENTITY_FIELD];
        
        foreach ($value as $document)
        {
            $identifier = $document->getValue($identityField);

            if (empty($identifier))
            {
                return FALSE;
            }
        }

        return TRUE;
    }
}
