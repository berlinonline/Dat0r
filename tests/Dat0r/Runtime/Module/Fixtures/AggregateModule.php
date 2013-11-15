<?php

namespace Dat0r\Tests\Runtime\Module\Fixtures;

use Dat0r\Runtime\Field\Type\TextField;
use Dat0r\Runtime\Field\Type\TextareaField;
use Dat0r\Runtime\Module\AggregateModule as BaseAggregateModule;

class AggregateModule extends BaseAggregateModule
{
    protected function __construct()
    {
        parent::__construct(
            'Paragraph',
            array(
                TextField::create('title'),
                TextareaField::create('content')
            )
        );
    }

    protected function getDocumentImplementor()
    {
        return '\\Dat0r\\Tests\\Runtime\\Document\\Fixtures\\DocumentTestProxy';
    }
}
