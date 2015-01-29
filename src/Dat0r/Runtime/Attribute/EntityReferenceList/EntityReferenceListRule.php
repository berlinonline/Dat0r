<?php

namespace Dat0r\Runtime\Attribute\EntityReferenceList;

use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;

/**
 * Validates that a given value consistently translates to a list of entities.
 *
 * Supported options: entity_types
 */
class EntityReferenceListRule extends Rule
{
    /**
     * Option that holds a list of allowed types to validate against.
     */
    const OPTION_ENTITY_TYPES = 'entity_types';

    /**
     * Validates and sanitizes a given value respective to the valueholder's expectations.
     *
     * @param mixed $value The types 'array' and 'EntityList' are accepted.
     *
     * @return boolean
     */
    protected function execute($value)
    {
        $success = true;
        $list = null;

        if ($value instanceof EntityList) {
            $list = $value;
        } elseif (null === $value) {
            $list = new EntityList();
        } elseif (is_array($value)) {
            $list = $this->createEntityList($value);
        } else {
            $this->throwError('invalid_type');
            $success = false;
        }

        if ($success) {
            $this->setSanitizedValue($list);
        }

        return $success;
    }

    /**
     * Create a EntityList from a given array of entity data.
     *
     * @param array $entities_data
     *
     * @return EntityList
     */
    protected function createEntityList(array $entities_data)
    {
        $type_map = array();
        foreach ($this->getOption(self::OPTION_ENTITY_TYPES, array()) as $type) {
            $type_map[$type->getEntityType()] = $type;
        }

        $list = new EntityList();
        ksort($entities_data);
        foreach ($entities_data as $entity_data) {
            if (!isset($entity_data[self::OBJECT_TYPE])) {
                $this->throwError('missing_doc_type', array(), IncidentInterface::CRITICAL);
                continue;
            }

            $embed_type = $entity_data[self::OBJECT_TYPE];
            unset($entity_data['@type']);

            if ($embed_type{0} !== '\\') {
                $embed_type = '\\' . $embed_type;
            }
            if (!isset($type_map[$embed_type])) {
                $this->throwError(
                    'invalid_doc_type',
                    array('type' => @$entity_data[self::OBJECT_TYPE]),
                    IncidentInterface::NOTICE
                );
                continue;
            }

            $embed_type = $type_map[$embed_type];
            $list->push($embed_type->createEntity($entity_data));
        }

        return $list;
    }
}
