<?php

namespace Workflowable\Workflow\Contracts;

interface WorkflowConditionTypeManagerContract
{
    public function register(WorkflowConditionTypeContract $workflowConditionTypeContract): self;

    public function isRegistered(string $alias): bool;

    public function getImplementations(): array;

    public function getImplementation(string $alias): WorkflowConditionTypeContract;

    public function getRules(string $alias): array;

    public function isValid(string $alias, array $data): bool;
}
