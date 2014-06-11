<?php

namespace Dat0r\Tests\Runtime\Module\Fixtures;

use Dat0r\Runtime\Field\Type\TextField;
use Dat0r\Runtime\Field\Type\TextareaField;
use Dat0r\Runtime\Module\ReferenceModule;

class CategoryModule extends ReferenceModule
{
    public function __construct()
    {
        parent::__construct(
            'Category',
            array(
                new TextField('title'),
                new TextareaField('description')
            )
        );
    }

    protected function getDocumentImplementor()
    {
        return '\\Dat0r\\Tests\\Runtime\\Document\\Fixtures\\DocumentTestProxy';
    }
}
