<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\EntityType;
use Dat0r\Runtime\Attribute\Type\Text;

class WorkflowTicketType extends EntityType
{
    public function __construct()
    {
        parent::__construct(
            'WorkflowTicket',
            array(
                new Text('workflow_name'),
                new Text('workflow_step')
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

    /**
     * Returns the EntityInterface implementor to use when creating new documents.
     *
     * @return string Fully qualified name of an EntityInterface implementation.
     */
    protected function getEntityImplementor()
    {
        return '\\Dat0r\\Tests\\Runtime\\Fixtures\\WorkflowTicket';
    }
}
