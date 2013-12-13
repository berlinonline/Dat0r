<?php

namespace Dat0r\Runtime\Validator\Rule\Type;

use Dat0r\Runtime\Document\DocumentList;
use Dat0r\Runtime\Module\PartialModule;
use Dat0r\Runtime\Document\PartialDocument;
use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Result\IIncident;

class ReferenceRule extends Rule
{
    /**
     * Valdiates and sanitizes a given value respective to the reference-valueholder's expectations.
     *
     * @param mixed $value The types 'array' and 'DocumentList' are accepted.
     *
     * @return boolean
     */
    protected function execute($value)
    {
        $success = true;
        $collection = null;

        if ($value instanceof DocumentList) {
            $collection = $value;
        } elseif (empty($value)) {
            $collection = new DocumentList();
        } else {
            $this->throwError('invalid_structure');
            $success = false;
        }

        if ($success) {
            $this->setSanitizedValue($collection);
        }

        return $success;
    }
}
