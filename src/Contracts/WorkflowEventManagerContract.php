<?php

namespace Workflowable\Workflow\Contracts;

interface WorkflowEventManagerContract
{
    public function register(WorkflowEventContract $workflowEventContract): self;

    public function isRegistered(string $alias): bool;

    public function getImplementations(): array;

    public function getImplementationByAlias(string $alias): WorkflowEventContract;

    public function getRules(string $alias): array;

    public function isValid(string $alias, array $data): bool;
}
