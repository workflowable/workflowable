# Workflow Processes

**Workflow processes** embody the execution of a workflow, representing instances created when a workflow is triggered or initiated. They encapsulate the data needed to execute activities and advance the workflow through different states.

## States

| ID | Name       | Description                                                                     |
|----|------------|---------------------------------------------------------------------------------|
| 1  | Created    | Indicates the creation of the process, not yet ready for processing              |
| 2  | Pending    | Indicates readiness for processing                                              |
| 3  | Dispatched | Indicates dispatch to the queue                                                 |
| 4  | Running    | Actively attempting to run the process                                           |
| 5  | Paused     | Work on the process is temporarily halted                                       |
| 6  | Failed     | An error occurred along the way                                                  |
| 7  | Completed  | All work for the process is concluded                                           |
| 8  | Cancelled  | The workflow process was cancelled                                              |

## Tokens

In a workflow process, tokens dynamically set key-value pairs, maintaining data relevant to a specific workflow process. The Workflowable package introduces two types of workflow tokens.

#### Input Tokens
Input tokens represent data passed into the workflow process from an external source. Usually, these tokens are defined during the creation of the workflow process and associated with the workflow event.

#### Output Tokens
Output tokens are created by the workflow process itself. They are useful for storing information, like an approval request ID, which can be accessed later in the workflow.

## Race Conditions

Due to the dynamic nature of workflows, multiple processes might interact with the same data, leading to potential conflicts. To mitigate this, you can implement the `ShouldPreventOverlappingWorkflowProcesses` interface, creating a custom key that leverages Laravel's `WithoutOverlapping` middleware.

```php
public function getWorkflowProcessLockKey(): string
{
    return 'my-custom-lock-key';
}
```

## Interacting With Processes

#### Triggering a Workflow Event

When a workflow event is triggered, a new workflow process is created in the 'created' state.

```php
$workflowEvent = new WorkflowEventClass($yourParameters = []);
Workflowable::triggerEvent($workflowEvent);
```

#### Dispatching Workflow Processes

Workflow processes are dispatched to the queue using the command `php artisan workflowable:process-runs`. If you need to dispatch a workflow process manually, use the following:

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
$queue = 'default';
Workflowable::dispatchProcess($workflowProcess, $queue);
```

#### Cancelling a Workflow Process

In some cases, you may need to cancel a workflow process. You can achieve this as follows:

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
CancelWorkflowProcessAction::make()->handle($workflowProcess);
```

#### Pausing/Resuming a Workflow Process

You can pause a workflow process in situations where work needs to halt until another task completes or an external action triggers resumption:

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
PauseWorkflowProcessAction::make()->handle($workflowProcess);
```

To resume the workflow process when ready:

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
ResumeWorkflowProcessAction::make()->handle($workflowProcess);
```

## Workflow Process Activity Logging

Workflow process activity logs provide insights into the execution attempts of activities within a workflow process. The `workflow_process_activity_logs` table captures essential information regarding each attempt, helping to monitor and analyze the performance of individual workflow activities.

### Utilizing Workflow Activity Attempts
Workflow process activity logs play a crucial role in understanding the execution history of activities within a workflow. This information is valuable for:

- **Monitoring Performance:** Evaluate the success and failure rates of activities to identify potential bottlenecks or issues in the workflow process.
- **Troubleshooting:** Investigate the details of failed attempts to diagnose and address issues promptly.
- **Analyzing Execution Time:** Track the duration of attempts to assess the efficiency of individual activities.

### Example Queries

1. Retrieve all attempts for a specific workflow process:

   ```sql
   SELECT * FROM workflow_process_activity_logs WHERE workflow_process_id = {workflow_process_id};
   ```

2. Identify failed attempts:

   ```sql
   SELECT * FROM workflow_process_activity_logs WHERE workflow_process_activity_log_status_id = {failure_status_id};
   ```

3. Analyze the average execution time for a particular activity:

   ```sql
   SELECT AVG(TIMESTAMPDIFF(SECOND, started_at, completed_at)) AS avg_execution_time
   FROM workflow_process_activity_logs
   WHERE workflow_activity_id = {activity_id};
   ```

These queries are illustrative examples and can be customized based on specific requirements and criteria. The information stored in `workflow_process_activity_logs` is instrumental in gaining insights into the performance and execution details of activities within a workflow process.
