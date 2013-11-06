<?php

namespace Dat0r\Core\Validator;

use Dat0r\Core\Document\IDocument;
use Dat0r\Core\Document\DocumentList;
use Dat0r\Core\Field\ReferenceField;

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
        if (!($value instanceof DocumentList)) {
            return false;
        }

        $reference_map = array();
        $references = $this->getField()->getOption(ReferenceField::OPT_REFERENCES);
        foreach ($references as $reference) {
            $implementor = $reference[ReferenceField::OPT_MODULE];
            if ($implementor{0} === '\\') {
                $replace_count = 1;
                $implementor = substr($implementor, 1);
            }
            $reference_map[$implementor] = $reference[ReferenceField::OPT_IDENTITY_FIELD];
        }

        foreach ($value as $document) {
            $module = $document->getModule();
            $moduleImplementor = get_class($module);

            if (!isset($reference_map[$moduleImplementor])) {
                return false;
            }

            $identifierField = $reference_map[$moduleImplementor];
            $identifier = $document->getValue($identifierField);

            if (empty($identifier)) {
                return false;
            }
        }

        return true;
    }
}
