<?php

namespace Dat0r\Tests\Runtime\Module\Fixtures;

use Dat0r\Runtime\Attribute\Type\Text;
use Dat0r\Runtime\Attribute\Type\Textarea;
use Dat0r\Runtime\Module\AggregateModule;

class ParagraphModule extends AggregateModule
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
