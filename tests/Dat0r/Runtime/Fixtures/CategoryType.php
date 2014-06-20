<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Runtime\Attribute\Type\Text;
use Dat0r\Runtime\Attribute\Type\Textarea;
use Dat0r\Runtime\DocumentType;

class CategoryType extends DocumentType
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
        return '\\Dat0r\\Tests\\Runtime\\Fixtures\\Category';
    }
}
