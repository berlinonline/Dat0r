<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Runtime\Document\Document;

class WorkflowTicket extends Document
{
    public function getWorkflowName()
    {
        return $this->getValue('workflow_name');
    }

    public function setWorkflowName($workflow_name)
    {
        return $this->setValue('workflow_name', $workflow_name);
    }

    public function getWorkflowStep()
    {
        return $this->getValue('workflow_step');
    }

    public function setWorkflowStep($workflow_step)
    {
        return $this->setValue('workflow_step', $workflow_step);
    }
}