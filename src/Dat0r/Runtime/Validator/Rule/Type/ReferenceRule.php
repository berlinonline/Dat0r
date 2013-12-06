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
        // prepare for mapping reference data to partial documents.
        $module_map = array();
        $reference_mappings = $this->getOption('reference_mappings', array());
        foreach ($reference_mappings as $reference_mapping) {
            $reference_type = $reference_mapping['module'];
            $referenced_module = $reference_type::getInstance();
            $id_fieldname = $reference_mapping['identity_field'];
            $label_fieldname = $reference_mapping['display_field'];
            $partial_fieldnames = array($id_fieldname, $label_fieldname);
            if (isset($reference_mapping['partial_fields']) && is_array($reference_mapping['partial_fields'])) {
                $partial_fieldnames = array_merge($partial_fieldnames, $reference_mapping['partial_fields']);
            }
            $partial_fields = array();
            foreach ($partial_fieldnames as $partial_fieldname) {
                $partial_fields[] = clone $referenced_module->getField($partial_fieldname);
            }
            $module_map[$referenced_module->getPrefix()] = array(
                'module' => $referenced_module,
                'partial_module' => new PartialModule(
                    $referenced_module->getName(),
                    $partial_fields,
                    array('document_implementor' => '\\Dat0r\\Runtime\\Document\\PartialDocument')
                )
            );
        }
        // then map the given reference data to partial documents and add them to the collection.
        $collection = new DocumentList();
        ksort($documents_data);
        foreach ($documents_data as $reference_data) {
            if (!isset($reference_data['module'])) {
                $this->throwError('missing_doc_type', array(), IIncident::CRITICAL);
                break;
            }
            if (!isset($reference_data['id'])) {
                $this->throwError('missing_doc_id', array(), IIncident::CRITICAL);
                break;
            }
            $reference_type = $reference_data['module'];
            if (!isset($module_map[$reference_type])) {
                $this->throwError('invalid_doc_type', array('type' => $reference_data['module']), IIncident::NOTICE);
                continue;
            }
            $referenced_module = $module_map[$reference_type]['module'];
            $partial_data = array('identifier' => $reference_data['id']);
            $partial_module = $module_map[$reference_type]['partial_module'];
            $collection->push($partial_module->createDocument($partial_data));
        }

        return $collection;
    }
}
