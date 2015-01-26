<?php

namespace Dat0r\Runtime\Validator\Rule\Type;

use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Result\IncidentInterface;

/**
 * AggregateRule validates that a given value consistently translates to a collection of entities.
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
     * @param mixed $value The types 'array' and 'EntityList' are accepted.
     *
     * @return boolean
     */
    protected function execute($value)
    {
        $success = true;
        $collection = null;

        if ($value instanceof EntityList) {
            $collection = $value;
        } elseif (null === $value) {
            $collection = new EntityList();
        } elseif (is_array($value)) {
            $collection = $this->createEntityList($value);
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
     * Create a EntityList from a given array of entity data.
     *
     * @param array $entities_data
     *
     * @return EntityList
     */
    protected function createEntityList(array $entities_data)
    {
        $type_map = array();
        foreach ($this->getOption(self::OPTION_AGGREGATE_MODULES, array()) as $type) {
            $type_map[$type->getEntityType()] = $type;
        }

        $collection = new EntityList();
        ksort($entities_data);
        foreach ($entities_data as $entity_data) {
            if (!isset($entity_data[self::OBJECT_TYPE])) {
                $this->throwError('missing_doc_type', array(), IncidentInterface::CRITICAL);
                continue;
            }

            $aggregate_type = $entity_data[self::OBJECT_TYPE];
            unset($entity_data['@type']);

            if ($aggregate_type{0} !== '\\') {
                $aggregate_type = '\\' . $aggregate_type;
            }
            if (!isset($type_map[$aggregate_type])) {
                $this->throwError(
                    'invalid_doc_type',
                    array('type' => @$entity_data[self::OBJECT_TYPE]),
                    IncidentInterface::NOTICE
                );
                continue;
            }

            $aggregate_type = $type_map[$aggregate_type];
            $collection->push($aggregate_type->createEntity($entity_data));
        }

        return $collection;
    }
}
