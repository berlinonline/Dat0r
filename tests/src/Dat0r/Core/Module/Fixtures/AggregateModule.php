<?php

namespace Dat0r\Tests\Core\Module\Fixtures;

use Dat0r\Runtime\Field;
use Dat0r\Runtime\Module;

class AggregateModule extends Module\AggregateModule
{
    protected function __construct()
    {
        parent::__construct(
            'Paragraph',
            array(
                Field\TextField::create('title'),
                Field\TextareaField::create('content')
            )
        );
    }

    protected function getDocumentImplementor()
    {
        return '\\Dat0r\\Tests\\Core\\Document\\Fixtures\\DocumentTestProxy';
    }
}
