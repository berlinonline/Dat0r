<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Attribute\Textarea\TextareaAttribute;
use Dat0r\Runtime\EntityType;
use Dat0r\Runtime\EntityTypeInterface;

class ParagraphType extends EntityType
{
    public function __construct(EntityTypeInterface $parent)
    {
        $parent_attr = $parent->getAttribute('content_objects');

        parent::__construct(
            'Paragraph',
            [
                new TextAttribute('title', $this, [], $parent_attr),
                new TextareaAttribute('content', $this, [], $parent_attr)
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
            $parent
        );
    }

    protected function getEntityImplementor()
    {
        return Paragraph::CLASS;
    }
}
