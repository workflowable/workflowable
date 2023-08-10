# Workflow Processes

**Workflow processes** represent the execution of a workflow. They are the instances of a workflow that are created 
when a workflow is triggered or initiated. They contain the data and information required to execute the workflow activities and progress the workflow through its various states.

## Workflow Process States

| ID | Name       | Description                                                                     |
|----|------------|---------------------------------------------------------------------------------|
| 1  | Created    | Indicates that we have created the process, but it is not ready to be picked up |
| 2  | Pending    | Indicates that it is ready to be process                                        |
| 3  | Dispatched | Indicates that we have dispatched the process to the queue                      |
| 4  | Running    | We are actively attempting to run the process                                   |
| 5  | Paused     | We've paused work on the process                                                |
| 6  | Failed     | There was an error along the way                                                |
| 7  | Completed  | We've concluded all work for the process                                        |
| 8  | Cancelled  | The workflow process was cancelled                                              |

## Triggering a Workflow Event

When a workflow event is triggered, a new workflow process is created for every active workflow.  The workflow
process is created in the `created` state.

```php
$workflowEvent = new WorkflowEventClass($yourParameters = []);
Workflowable::triggerEvent($workflowEvent);
```

## Creating a Workflow Process

If you need to create a workflow process manually, you can do so as follows:

```php
$workflowToCreateProcessFor = Workflow::find($workflowId);
$workflowEvent = new WorkflowEventClass($yourParameters = []);
Workflowable::createWorkflowProcess($workflowToCreateProcessFor, $workflowEvent);
```

## Dispatching Workflow Processes

Workflow processes are dispatched to the queue by the command `php artisan workflowable:process-runs`.  If you need to
dispatch a workflow process manually, you can do so as follows:

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
$queue = 'default';
Workflowable::dispatchProcess($workflowProcess, $queue);
```

## Cancelling a Workflow Process

In some cases, you may need to cancel a workflow process because you no longer need it to run.  You can accomplish this
as follows:


```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
Workflowable::cancelRun(WorkflowProcess $workflowProcess);
```

## Pausing a Workflow Process

In some cases, you may need to pause a workflow process.  This might be done if you need to stop work until 
something else in the system is completed like creating and executing a subprocess/parallel process, or waiting for 
an outside action to trigger resumption of the workflow process.

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
Workflowable::pauseRun(WorkflowProcess $workflowProcess);
```

When you are ready to resume the workflow process, you can do so as follows:

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
Workflowable::resumeRun(WorkflowProcess $workflowProcess);
```
