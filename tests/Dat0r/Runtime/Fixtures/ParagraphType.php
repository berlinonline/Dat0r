<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\Attribute\Type\Text;
use Dat0r\Runtime\Attribute\Type\Textarea;
use Dat0r\Runtime\EntityType;

class ParagraphType extends EntityType
{
    public function __construct()
    {
        parent::__construct(
            'Paragraph',
            array(
                new Text('title'),
                new Textarea('content')
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
        return '\\Dat0r\\Tests\\Runtime\\Fixtures\\Paragraph';
    }
}
