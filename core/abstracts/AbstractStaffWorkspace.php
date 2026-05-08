<?php

abstract class AbstractStaffWorkspace extends AbstractModel
{
    private array $assignmentContext = [];

    public function getAssignmentContext(): array
    {
        return $this->assignmentContext;
    }

    protected function setAssignmentContext(array $context): void
    {
        $this->assignmentContext = $context;
    }
}

