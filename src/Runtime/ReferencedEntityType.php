<?php

namespace Dat0r\Runtime;

use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\OptionsInterface;
use Dat0r\Runtime\Attribute\AttributeInterface;

abstract class ReferencedEntityType extends EntityType
{
    const OPTION_IDENTIFYING_ATTRIBUTE_NAME = 'identifying_attribute';

    const OPTION_REFERENCED_TYPE_CLASS = 'referenced_type';

    public function __construct(
        $name,
        array $attributes = [],
        OptionsInterface $options = null,
        EntityTypeInterface $parent = null,
        AttributeInterface $parent_attribute = null
    ) {
        parent::__construct($name, $attributes, $options, $parent, $parent_attribute);

        if (!$this->hasOption(self::OPTION_IDENTIFYING_ATTRIBUTE_NAME)) {
            throw new RuntimeException(
                sprintf('Missing expected option "%s"', self::OPTION_IDENTIFYING_ATTRIBUTE_NAME)
            );
        }

        if (!$this->hasOption(self::OPTION_REFERENCED_TYPE_CLASS)) {
            throw new RuntimeException(
                sprintf('Missing expected option "%s"', self::OPTION_REFERENCED_TYPE_CLASS)
            );
        }
    }

    public function getReferenceIdentifyingAttributeName()
    {
        return $this->getOption('referenced_attribute');
    }

    public function getReferencedTypeClass()
    {
        return $this->getOption('referenced_type');
    }
}
