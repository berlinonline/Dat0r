<?php

namespace Dat0r\Tests\Runtime\Type\Fixtures;

use Dat0r\Runtime\Attribute\Type\Text;
use Dat0r\Runtime\Attribute\Type\Textarea;
use Dat0r\Runtime\Type\Aggregate;

class ParagraphType extends Aggregate
{
    public function __construct()
    {
        parent::__construct(
            'Paragraph',
            array(
                new Text('title'),
                new Textarea('content')
            )
        );
    }

    protected function getDocumentImplementor()
    {
        return '\\Dat0r\\Tests\\Runtime\\Document\\Fixtures\\DocumentTestProxy';
    }
}
