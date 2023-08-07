# Race Conditions

We take a number of steps to prevent race conditions in workflow processes by default.  For example, we use a atomic 
lock to
prevent the same workflow process from being executed at the same time. There are scenarios you will encounter where this
alone will not be enough and you'll need to take additional steps to prevent overlapping workflow processes and we provide
a number of tools to help you do this.

## Prevent Overlapping Workflow Processes For A Specific Workflow Event Class

When you create a workflow event class, you can use the `Workflowable\Workflowable\Traits\PreventOverlappingWorkflowRuns`
trait to prevent overlapping workflow processes for that specific workflow event class.  This trait will prevent a workflow
process from running if there is already a workflow process in progress for the same workflow event class.

## Preventing Overlapping Workflow Processes Touching The Same Database Records

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
