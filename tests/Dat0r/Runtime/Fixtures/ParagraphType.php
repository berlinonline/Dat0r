<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Attribute\Textarea\TextareaAttribute;
use Dat0r\Runtime\EntityType;

class ParagraphType extends EntityType
{
    public function __construct()
    {
        parent::__construct(
            'Paragraph',
            array(
                new TextAttribute('title'),
                new TextareaAttribute('content')
            ),
            new Options(
                array(
                    'foo' => 'bar',
                    'nested' => array(
                        'foo' => 'bar',
                        'blah' => 'blub'
                    )
                )
            )
        );
    }

    protected function getEntityImplementor()
    {
        return Paragraph::CLASS;
    }
}
