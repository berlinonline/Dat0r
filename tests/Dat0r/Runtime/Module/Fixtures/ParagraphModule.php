<?php

namespace Dat0r\Tests\Runtime\Module\Fixtures;

use Dat0r\Runtime\Field\Type\TextField;
use Dat0r\Runtime\Field\Type\TextareaField;
use Dat0r\Runtime\Module\AggregateModule;

class ParagraphModule extends AggregateModule
{
    public function __construct()
    {
        parent::__construct(
            'Paragraph',
            array(
                new TextField('title'),
                new TextareaField('content')
            )
        );
    }

    protected function getDocumentImplementor()
    {
        return '\\Dat0r\\Tests\\Runtime\\Document\\Fixtures\\DocumentTestProxy';
    }
}
