<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\Attribute\Type\Text;
use Dat0r\Runtime\Attribute\Type\Textarea;
use Dat0r\Runtime\EntityType;

class CategoryType extends EntityType
{
    public function __construct()
    {
        parent::__construct(
            'Category',
            array(
                new Text('title'),
                new Textarea('description')
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
        return '\\Dat0r\\Tests\\Runtime\\Fixtures\\Category';
    }
}
