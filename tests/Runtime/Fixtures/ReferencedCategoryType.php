<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\EntityType;
use Dat0r\Runtime\EntityTypeInterface;
use Dat0r\Runtime\ReferencedEntityTypeInterface;

class ReferencedCategoryType extends EntityType implements ReferencedEntityTypeInterface
{
    public function __construct(EntityTypeInterface $parent, AttributeInterface $parent_attribute)
    {
        parent::__construct(
            'ReferencedCategory',
            [
                new TextAttribute('identifier', $this, [], $parent_attribute),
                new TextAttribute('referenced_identifier', $this, [], $parent_attribute)
            ],
            new Options(
                [ 'referenced_type' => CategoryType::CLASS, 'identifying_attribute' => 'identifier' ]
            ),
            $parent,
            $parent_attribute
        );
    }

    public static function getEntityImplementor()
    {
        return ReferencedCategory::CLASS;
    }

    public function getReferencedAttributeName()
    {
        return 'categories';
    }

    public function getReferencedTypeClass()
    {
        return CategoryType::CLASS;
    }
}
