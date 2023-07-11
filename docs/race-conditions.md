# Race Conditions

We take a number of steps to prevent race conditions in workflow runs by default.  For example, we use a atomic 
lock to
prevent the same workflow run from being executed at the same time. There are scenarios you will encounter where this
alone will not be enough and you'll need to take additional steps to prevent overlapping workflow runs and we provide
a number of tools to help you do this.

## Prevent Overlapping Workflow Runs For A Specific Workflow Event Class

When you create a workflow event class, you can use the `Workflowable\Workflowable\Traits\PreventOverlappingWorkflowRuns`
trait to prevent overlapping workflow runs for that specific workflow event class.  This trait will prevent a workflow
run from running if there is already a workflow run in progress for the same workflow event class.

## Preventing Overlapping Workflow Runs Touching The Same Database Records

If you have a workflow that touches the same database records, you may want to prevent overlapping workflow runs from
running at the same time.  For example, let's say you have two workflows that update the state of a record.  Those
workflows have conditions on their transitions to ensure that only that the correct state will be chosen.  However,
if both workflows run at the same time, it's possible that the state of the record will be updated by one workflow
and then updated again by the other workflow before the first workflow has a chance to complete.  This can be prevented
by using the `Workflowable\Workflowable\Traits\PreventsOverlappingWorkflowRuns` trait.  This trait offers
a by default uses the workflow event alias to ensure that only one workflow run belonging to an event alias can be run
at a time, but you can override this behavior by implementing the `getWorkflowRunLockKeys` method on your workflow event
class like so:

```php
public function getWorkflowRunLockKey(): string
{
    return 'my-custom-lock-key';
}
```
