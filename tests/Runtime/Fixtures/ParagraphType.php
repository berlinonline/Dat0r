<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Attribute\Textarea\TextareaAttribute;
use Dat0r\Runtime\EntityType;
use Dat0r\Runtime\EntityTypeInterface;

class ParagraphType extends EntityType
{
    public function __construct(EntityTypeInterface $parent, AttributeInterface $parent_attribute)
    {
        parent::__construct(
            'Paragraph',
            [
                new TextAttribute('title', $this, [], $parent_attribute),
                new TextareaAttribute('content', $this, [], $parent_attribute)
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

    public function getEntityImplementor()
    {
        return Paragraph::CLASS;
    }
}
