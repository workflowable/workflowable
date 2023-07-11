# Setup

## Creating Workflow Runs

The easiest way is by simply triggering the event. This can be done as follows:

```php
$workflowEvent = new WorkflowEventClass($yourParameters = []);
Workflowable::triggerEvent($workflowEvent);
```

## Dispatching Workflow Runs

## Infrastructure

To ensure that all of your workflow runs process to completion, you need to set up the following:

- You must set up your Laravel queue. Information on setting this up can be found in [Laravel's Queue documentation](https://laravel.com/docs/10.x/queues).
- You should ensure that the following command is run every minute to ensure that we are dispatching the `WorkflowRunnerJob` on regular intervals. Ideally, you would set it to be run every minute on a single server and prevent overlapping. You can read about this in [Laravel's Task Scheduling documentation](https://laravel.com/docs/10.x/scheduling).
