<?php

namespace Dat0r\Runtime\Attribute\EntityReferenceList;

use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListRule;
use Dat0r\Runtime\Entity\EntityInterface;
use Dat0r\Common\Error\RuntimeError;
use Dat0r\Runtime\Entity\EntityReferenceInterface;

/**
 * Validates that a given value consistently translates to a list of entities.
 *
 * Supported options: entity_types
 */
class EntityReferenceListRule extends EmbeddedEntityListRule
{
}
