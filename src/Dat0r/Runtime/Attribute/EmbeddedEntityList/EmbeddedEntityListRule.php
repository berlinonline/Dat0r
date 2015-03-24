<?php

namespace Dat0r\Runtime\Attribute\EmbeddedEntityList;

use Dat0r\Common\Object;
use Dat0r\Runtime\Entity\EntityInterface;
use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;

/**
 * Validates that a given (array) value consistently translates to a list of entities.
 *
 * Supported options: entity_types
 */
class EmbeddedEntityListRule extends Rule
{
    /**
     * Option that holds a list of allowed types to validate against.
     */
    const OPTION_ENTITY_TYPES = 'entity_types';
    const OPTION_MAX_COUNT = 'max_count';
    const OPTION_MIN_COUNT = 'min_count';

    const OBJECT_TYPE = Object::OBJECT_TYPE;

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
        } elseif ($value instanceof EntityInterface) {
            $list = new EntityList();
            $list->push($value);
        } elseif (is_array($value)) {
            $list = new EntityList();
            $success = $this->createEntityList($value, $list);
        } else {
            $this->throwError('invalid_type');
            return false;
        }

        $count = count($list);
        if ($this->hasOption(self::OPTION_MIN_COUNT)) {
            $min_count = $this->getOption(self::OPTION_MIN_COUNT, 0);
            if ($count < (int)$min_count) {
                $this->throwError('min_count', [ 'count' => $count, 'min_count' => $min_count ]);
                $success = false;
            }
        }

        if ($this->hasOption(self::OPTION_MAX_COUNT)) {
            $max_count = $this->getOption(self::OPTION_MAX_COUNT, 0);
            if ($count > (int)$max_count) {
                $this->throwError('max_count', [ 'count' => $count, 'max_count' => $max_count ]);
                $success = false;
            }
        }

        if ($success) {
            $this->setSanitizedValue($list);
            return true;
        }

        return false;
    }

    /**
     * Create a EntityList from a given array of entity data.
     *
     * @param array $entities_data
     *
     * @return EntityList
     */
    protected function createEntityList(array $entities_data, EntityList $list)
    {
        $success = true;

        $type_map = [];
        foreach ($this->getOption(self::OPTION_ENTITY_TYPES, []) as $type) {
            $trimmed_type_name = trim($type->getEntityType(), '\\');
            $type_map[$trimmed_type_name] = $type;
        }

        foreach ($entities_data as $entity_data) {
            if (!isset($entity_data[self::OBJECT_TYPE])) {
                $success = false;
                $this->throwError('missing_doc_type', [], IncidentInterface::CRITICAL);
                continue;
            }

            $trimmed_embed_type = trim($entity_data[self::OBJECT_TYPE], '\\');
            unset($entity_data['@type']);

            if (!isset($type_map[$trimmed_embed_type])) {
                //var_dump(array_keys($type_map), $trimmed_embed_type);exit;
                $success = false;
                $this->throwError(
                    'invalid_doc_type',
                    [ 'type' => var_export($entity_data[self::OBJECT_TYPE], true) ],
                    IncidentInterface::NOTICE
                );
                continue;
            }

            $embed_type = $type_map[$trimmed_embed_type];
            $list->push($embed_type->createEntity($entity_data));
        }

        return $success;
    }
}
