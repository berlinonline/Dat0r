<?php

namespace Dat0r\Runtime\Attribute\Validator\Rule\Type;

use Dat0r\Runtime\Document\DocumentList;
use Dat0r\Runtime\Attribute\Validator\Rule\Rule;
use Dat0r\Runtime\Attribute\Validator\Result\IIncident;

/**
 * AggregateRule validates that a given value consistently translates to a collection of documents.
 *
 * Supported options: aggregate_types
 */
class AggregateRule extends Rule
{
    /**
     * Option that holds a list of allowed types to validate against.
     */
    const OPTION_AGGREGATE_MODULES = 'aggregate_types';

    /**
     * Valdiates and sanitizes a given value respective to the aggregate-valueholder's expectations.
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
        } elseif (null === $value) {
            $collection = new DocumentList();
        } elseif (is_array($value)) {
            $collection = $this->createDocumentList($value);
        } else {
            $this->throwError('invalid_type');
            $success = false;
        }

        if ($success) {
            $this->setSanitizedValue($collection);
        }

        return $success;
    }

    /**
     * Create a DocumentList from a given array of document data.
     *
     * @param array $documents_data
     *
     * @return DocumentList
     */
    protected function createDocumentList(array $documents_data)
    {
        $type_map = array();
        foreach ($this->getOption(self::OPTION_AGGREGATE_MODULES, array()) as $type) {
            $type_map[$type->getDocumentType()] = $type;
        }

        $collection = new DocumentList();
        ksort($documents_data);
        foreach ($documents_data as $document_data) {
            if (!isset($document_data[self::OBJECT_TYPE])) {
                $this->throwError('missing_doc_type', array(), IIncident::CRITICAL);
                continue;
            }

            $aggregate_type = $document_data[self::OBJECT_TYPE];
            unset($document_data['@type']);

            if ($aggregate_type{0} !== '\\') {
                $aggregate_type = '\\' . $aggregate_type;
            }
            if (!isset($type_map[$aggregate_type])) {
                $this->throwError(
                    'invalid_doc_type',
                    array('type' => @$document_data[self::OBJECT_TYPE]),
                    IIncident::NOTICE
                );
                continue;
            }

            $aggregate_type = $type_map[$aggregate_type];
            $collection->push($aggregate_type->createDocument($document_data));
        }

        return $collection;
    }
}
