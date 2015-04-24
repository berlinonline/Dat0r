<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Attribute\Textarea\TextareaAttribute;
use Dat0r\Runtime\EntityType;

class CategoryType extends EntityType
{
    public function __construct()
    {
        parent::__construct(
            'Category',
            [
                new TextAttribute('title', $this),
                new TextareaAttribute('description', $this)
            ],
            new Options(
                [
                    'foo' => 'bar',
                    'nested' => [
                        'foo' => 'bar',
                        'blah' => 'blub'
                    ]
                ]
            )
        );
    }

    protected function getEntityImplementor()
    {
        return Category::CLASS;
    }
}
