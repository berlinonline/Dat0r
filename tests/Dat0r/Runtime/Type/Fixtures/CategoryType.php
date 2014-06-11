<?php

namespace Dat0r\Tests\Runtime\Type\Fixtures;

use Dat0r\Runtime\Attribute\Type\Text;
use Dat0r\Runtime\Attribute\Type\Textarea;
use Dat0r\Runtime\Type\Reference;

class CategoryType extends Reference
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
