<?php

namespace Dat0r\Tests\Runtime\Module\Fixtures;

use Dat0r\Runtime\Attribute\Type\Text;
use Dat0r\Runtime\Attribute\Type\Textarea;
use Dat0r\Runtime\Module\ReferenceModule;

class CategoryModule extends ReferenceModule
{
    public function __construct()
    {
        parent::__construct(
            'Category',
            array(
                new Text('title'),
                new Textarea('description')
            )
        );
    }

    protected function getDocumentImplementor()
    {
        return '\\Dat0r\\Tests\\Runtime\\Document\\Fixtures\\DocumentTestProxy';
    }
}
