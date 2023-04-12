<?php

namespace Workflowable\Workflow\Contracts;

interface WorkflowConditionManagerContract
{
    public function register(WorkflowConditionContract $workflowCondition): self;

    public function isRegistered(string $alias): bool;

    public function getImplementations(): array;

    public function getImplementation(string $alias): WorkflowConditionContract;

    public function getRules(string $alias): array;

    public function isValid(string $alias, array $data): bool;
}
