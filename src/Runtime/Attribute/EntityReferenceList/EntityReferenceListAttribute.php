<?php

namespace Dat0r\Runtime\Attribute\EntityReferenceList;

use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Dat0r\Runtime\Entity\EntityReferenceInterface;
use Dat0r\Common\Error\RuntimeException;
use ReflectionClass;

/**
 * Allows to nest multiple types under a defined attribute_name.
 *
 * The corresponding internal value is a list of entities.
 *
 * Supported options: OPTION_ENTITY_TYPES (to specify allowed entity types)
 */
class EntityReferenceListAttribute extends EmbeddedEntityListAttribute
{
    protected function createEmbeddedTypeMap()
    {
        $entity_type_map = parent::createEmbeddedTypeMap();

        foreach ($entity_type_map as $embedded_typ) {
            $entity_type = $embedded_typ->getEntityImplementor();
            $entity_reflection = new ReflectionClass($entity_type);
            if (!$entity_reflection->implementsInterface(EntityReferenceInterface::CLASS)) {
                throw new RuntimeException(
                    sprintf(
                        'Invalid embedded-type given to %s. Only instance of %s accepted.',
                        $this->getName(),
                        EntityReferenceInterface::CLASS
                    )
                );
            }
        }

        return $entity_type_map;
    }
}
