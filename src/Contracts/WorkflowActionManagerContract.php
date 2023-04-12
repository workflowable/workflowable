<?php

namespace Workflowable\Workflow\Contracts;

interface WorkflowActionManagerContract
{
    public function register(WorkflowActionContract $workflowActionContract): self;

    public function getImplementations(): array;

    public function getImplementation(string $alias): WorkflowActionContract;

    public function getRules(string $alias): array;

    public function isValid(string $alias, array $data): bool;

    public function isRegistered(string $alias): bool;
}
