<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\EntityType;
use Dat0r\Runtime\EntityTypeInterface;
use Dat0r\Runtime\Attribute\AttributeInterface;

class WorkflowStateType extends EntityType
{
    public function __construct(EntityTypeInterface $parent, AttributeInterface $parent_attribute)
    {
        parent::__construct(
            'WorkflowState',
            [
                new TextAttribute('workflow_name', $this, [], $parent_attribute),
                new TextAttribute('workflow_step', $this, [], $parent_attribute)
            ],
            new Options(
                [
                    'foo' => 'bar',
                    'nested' => [
                        'foo' => 'bar',
                        'blah' => 'blub'
                    ]
                ]
            ),
            $parent,
            $parent_attribute
        );
    }

    /**
     * Returns the EntityInterface implementor to use when creating new documents.
     *
     * @return string Fully qualified name of an EntityInterface implementation.
     */
    protected function getEntityImplementor()
    {
        return WorkflowState::CLASS;
    }
}
