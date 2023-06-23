# Workflow Engine Documentation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/workflowable/workflow-engine.svg?style=flat-square)](https://packagist.org/packages/workflowable/workflow-engine)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/workflowable/workflow-engine/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/workflowable/workflow-engine/actions?query=workflow-engine%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/workflowable/workflow-engine/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/workflowable/workflow-engine/actions?query=workflow-engine%3A"Fix+PHP+code+style+issues"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/workflowable/workflow-engine.svg?style=flat-square)](https://packagist.org/packages/workflowable/workflow-engine)

## Introduction

At its core, a workflow engine orchestrates the sequence of activities required to accomplish a specific task or achieve a particular outcome. It allows users to design and model complex workflows by defining the steps, rules, and conditions that govern the flow of work. These workflows can range from simple, linear processes to highly intricate and conditional ones involving multiple participants, decision points, and integrations with various systems.

> **Note**: This is an early release and is not ready for production usage. APIs are subject to change. This early release aims to gather feedback and design suggestions while the core development is in progress. Documentation will be added as the APIs stabilize.

## What is a workflow event?

A workflow event refers to a specific occurrence or trigger that initiates or influences a workflow process. It represents an event or condition that leads to the execution of workflow steps, the progression of work, or the activation of certain workflow actions.

Workflow events can be internal or external to the system or application hosting the workflow. They can be triggered by system events, user actions, time-based events, data changes, or any other event that requires the workflow engine to respond and execute the appropriate workflow steps.

You can create a workflow event by invoking the following command:

```bash
php artisan make:workflow-event
```

## What is a workflow step?

A workflow step represents a specific action or unit of work within a workflow. It is a fundamental building block of a workflow, defining the individual actions or operations that need to be performed to complete a process or achieve a desired outcome.

### How do I define what a workflow step should be doing?

The definition of what a workflow step should perform is called a workflow step type. This data structure should be responsible for validating your parameters and identifying data dependencies in the workflow run parameters.

You can create a workflow step type by invoking the following command:

```bash
php artisan make:workflow-step-type {name}
```

## What is a workflow transition?

A workflow transition refers to the movement or progression of a workflow from one state or step to another. It represents the path or connection between two workflow steps, indicating the flow of work and the logical sequence of actions within a workflow.

### How can I add conditions to determine what transitions are eligible to be performed?

You can do this through one or more workflow conditions. Like workflow steps, workflow conditions have a workflow condition type that validates, defines dependencies, and handles executing the code to check whether a specific condition is met.

You can create a workflow condition type by invoking the following command:

```bash
php artisan make:workflow-condition-type {name}
```

## Managing Dependencies

When dealing with workflow condition types and workflow step types, you will likely come across a scenario where you need to scope them so that they are only available for one or more workflow events. You can accomplish this by providing the aliases to the following method:

```php
public function getWorkflowEventAliases(): array
{
    return [
        'workflow-event-alias'
    ];
}
```

Assuming you are reliant on the workflow to provide you certain parameters. You can also tell the workflow engine the data that you need in these classes by using the following method:

```php
public function getRequiredWorkflowEventKeys(): array
{
    return [
        'REQUIRED_KEY_HERE'
    ];
}
```

You can then test the integrity of your workflow by invoking the command and find out where any unmet dependencies exist in your workflow event configuration.

```bash
php artisan workflow-engine:verify-integrity {name}
```

## Building Workflow Components

Once all of your workflow events, step types, and condition types are configured, you can start building your actual workflow steps, conditions, and transitions. There are dedicated actions available for each of these operations, allowing you

to create and update them while ensuring that the data being passed to them meets the required rules defined in your configuration classes. See the following for more information:

- CreateWorkflowAction
- CreateWorkflowStepAction
- UpdateWorkflowStepAction
- CreateWorkflowTransitionAction
- UpdateWorkflowTransitionAction
- CreateWorkflowConditionAction
- UpdateWorkflowConditionAction

Once you have completed the setup of your workflow components, you need to activate your workflow. This can be done via:

- ActivateWorkflowAction
- SwapActiveWorkflowAction

## Creating a Workflow Run

The easiest way is by simply triggering the event. This can be done as follows:

```php
$workflowEvent = new WorkflowEventClass($yourParameters = []);
WorkflowEngine::triggerEvent($workflowEvent);
```

This will take a look at all of your active workflows for that specific event and create new workflow runs for each of those workflows.

## Infrastructure Setup

To ensure that all of your workflow runs process to completion, you need to set up the following:

- You must set up your Laravel queue. Information on setting this up can be found in [Laravel's Queue documentation](https://laravel.com/docs/10.x/queues).
- You should ensure that the following command is run every minute to ensure that we are dispatching the `WorkflowRunnerJob` on regular intervals. Ideally, you would set it to be run every minute on a single server and prevent overlapping. You can read about this in [Laravel's Task Scheduling documentation](https://laravel.com/docs/10.x/scheduling).

## Additional Information

- **Testing:** Use `composer test` to run the tests.
- **Changelog:** Please see the [CHANGELOG](https://github.com/workflowable/workflow-engine/blob/master/CHANGELOG.md) file for more information on what has changed recently.
- **Contributing:** Please see the [CONTRIBUTING](https://github.com/workflowable/workflow-engine/blob/master/CONTRIBUTING.md) file for details on how to contribute.
- **Security Vulnerabilities:** Please review our [security policy](https://github.com/workflowable/workflow-engine/security/policy) on how to report security vulnerabilities.

### Credits:
- [Andrew Leach](https://github.com/AndyLeach)
- [All contributors](https://github.com/workflowable/workflow-engine/contributors)

### License:
The MIT License (MIT). Please see the [License File](https://github.com/workflowable/workflow-engine/blob/master/LICENSE.md) for more information.
