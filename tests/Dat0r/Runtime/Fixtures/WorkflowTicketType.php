<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\DocumentType;
use Dat0r\Runtime\Attribute\Type\Text;

class WorkflowTicketType extends DocumentType
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
     * Returns the IDocument implementor to use when creating new documents.
     *
     * @return string Fully qualified name of an IDocument implementation.
     */
    protected function getDocumentImplementor()
    {
        return '\\Dat0r\\Tests\\Runtime\\Fixtures\\WorkflowTicket';
    }
}
