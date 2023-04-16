<?php

namespace Workflowable\Workflow\Contracts;

interface WorkflowStepTypeManagerContract
{
    public function register(WorkflowStepTypeContract $workflowStepTypeContract): self;

    public function getImplementations(): array;

    public function getImplementation(string $alias): WorkflowStepTypeContract;

    public function getRules(string $alias): array;

    public function isValid(string $alias, array $data): bool;

    public function isRegistered(string $alias): bool;

    public function getWorkflowEventAlias(string $alias): ?string;
}
