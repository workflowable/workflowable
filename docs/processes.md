# Workflow Processes

**Workflow processes** represent the execution of a workflow. They are the instances of a workflow that are created 
when a workflow is triggered or initiated. They contain the data and information required to execute the workflow activities and progress the workflow through its various states.

## States

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

## Tokens

In a workflow process, you can use tokens to dynamically set key value pairs.  This is useful for maintaining a dataset of data that is specifically relevant to a specific workflow process.  In the workflowable package, we have two
different types of workflow tokens.

#### Input Tokens
In general though, input tokens represent the data that is passed into the workflow process from a source other than the 
workflow process itself.  Most of the time, input tokens are defined at the time of creating the workflow process, and 
are defined on the workflow event associated with the workflow.

#### Output Tokens
Output tokens are tokens that were created by the workflow process itself.  You might need one of these if you want
to create a request for approval, and you want to store the approval request id on the workflow process, so you can
find the status of that request somewhere later in the workflow.

## Race Conditions

We take a number of steps to prevent race conditions in workflow processes by default.  For example, we use a atomic
lock to
prevent the same workflow process from being executed at the same time. There are scenarios you will encounter where this
alone will not be enough and you'll need to take additional steps to prevent overlapping workflow processes and we provide
a number of tools to help you do this.

#### Prevent Overlapping Workflow Processes For A Specific Workflow Event Class

When you create a workflow event class, you can use the
`Workflowable\Workflowable\Traits\PreventOverlappingWorkflowProcesses`
trait to prevent overlapping workflow processes for that specific workflow event class.  This trait will prevent a workflow
process from running if there is already a workflow process in progress for the same workflow event class.

#### Preventing Overlapping Workflow Processes Touching The Same Database Records

If you have a workflow that touches the same database records, you may want to prevent overlapping workflow processes from
running at the same time.  For example, let's say you have two workflows that update the state of a record.  Those
workflows have conditions on their transitions to ensure that only that the correct state will be chosen.  However,
if both workflows process at the same time, it's possible that the state of the record will be updated by one workflow
and then updated again by the other workflow before the first workflow has a chance to complete.  This can be prevented
by using the `Workflowable\Workflowable\Traits\PreventsOverlappingWorkflowProcesses` trait.  This trait offers
a by default uses the workflow event alias to ensure that only one workflow process belonging to an event alias can be run
at a time, but you can override this behavior by implementing the `getWorkflowProcessLockKeys` method on your workflow event
class like so:

```php
public function getWorkflowProcessLockKey(): string
{
    return 'my-custom-lock-key';
}
```

## Interacting With Processes

#### Triggering a Workflow Event

When a workflow event is triggered, a new workflow process is created for every active workflow.  The workflow
process is created in the `created` state.

```php
$workflowEvent = new WorkflowEventClass($yourParameters = []);
Workflowable::triggerEvent($workflowEvent);
```

#### Dispatching Workflow Processes
Workflow processes are dispatched to the queue by the command `php artisan workflowable:process-runs`.  If you need to
dispatch a workflow process manually, you can do so as follows:

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
$queue = 'default';
Workflowable::dispatchProcess($workflowProcess, $queue);
```

#### Cancelling a Workflow Process

In some cases, you may need to cancel a workflow process because you no longer need it to run.  You can accomplish this
as follows:

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
CancelWorkflowProcessAction::make()->handle(WorkflowProcess $workflowProcess);
```

#### Pausing/Resuming a Workflow Process

In some cases, you may need to pause a workflow process.  This might be done if you need to stop work until 
something else in the system is completed like creating and executing a subprocess/parallel process, or waiting for 
an outside action to trigger resumption of the workflow process.

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
PauseWorkflowProcessAction::make()->handle(WorkflowProcess $workflowProcess);
```

When you are ready to resume the workflow process, you can do so as follows:

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
ResumeWorkflowProcessAction::make()->handle(WorkflowProcess $workflowProcess);
```
