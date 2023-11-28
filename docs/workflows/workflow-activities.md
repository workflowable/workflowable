# Workflow Activities

A **workflow activity** represents a specific action or unit of work within a workflow. It is a fundamental building block
of a workflow, defining the individual actions or operations that need to be performed to complete a process or achieve a desired outcome.

## How do I define what a workflow activity should be doing?

The definition of what a workflow activity should perform is called a **workflow activity type**. This data structure should be
responsible for validating your parameters and identifying data dependencies in the workflow run parameters.

## How do I create a workflow activity type?

You can create a new workflow activity type by running the following command:

```bash
php artisan make:make:workflow-activity-type {name}
```

This will generate a new class in the `app/Workflows/WorkflowActivityTypes` directory. You can then define the logic 
for you to begin writing your new workflow activity type.

## How do I register a workflow activity type?

You can register a workflow activity type by adding it to the `workflowable.workflow_activity_types` config file.
