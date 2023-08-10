# Workflow Conditions

A workflow condition is a data structure that defines the logic for determining whether a workflow transition is eligible to be performed. It is a fundamental building block of a workflow, defining the rules that determine the flow of work and the logical sequence of actions within a workflow.

## How do I define what a workflow condition should be doing?

The definition of what a workflow condition should perform is called a **workflow condition type**. This data 
structure should be responsible for validating your parameters and identifying data dependencies in the workflow run 
parameters. Additionally, it should be responsible for executing the code to check whether a specific condition is met.

## How do I create a workflow condition type?

You can create a new workflow condition type by running the following command:

```bash
php artisan make:make:workflow-condition-type {name}
```

This will generate a new class in the `app/Workflows/WorkflowConditionTypes` directory. You can then define the logic
for you to begin writing your new workflow activity type.

## How do I register a workflow condition type?

You can register a workflow condition type by adding it to the `workflowable.workflow_condition_types` config file.
