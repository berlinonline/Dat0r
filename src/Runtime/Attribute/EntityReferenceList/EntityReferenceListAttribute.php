<?php

namespace Dat0r\Runtime\Attribute\EntityReferenceList;

use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;

/**
 * Allows to nest multiple types under a defined attribute_name.
 *
 * The corresponding internal value is a list of entities.
 *
 * Supported options: OPTION_ENTITY_TYPES (to specify allowed entity types)
 */
class EntityReferenceListAttribute extends EmbeddedEntityListAttribute
{
}
