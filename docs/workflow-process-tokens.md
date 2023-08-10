# Workflow Processes Tokens

In a workflow process, you can use tokens to dynamically set values.  This is useful for things like setting the
assignee of a task to the user who triggered the workflow process.  In the workflowable package, we have two 
different types of workflow tokens.

## Input Tokens
Most of the time, input tokens are defined at the time of creating the workflow process, and are defined on the 
workflow event associated with the workflow.  In general though, input tokens represent the data that is passed into
the workflow process from a source other than the workflow process itself.  You can create an input token manually
as follows:

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
Workflowable::createInputToken($workflowProcess, 'token_key', 'token_value');
```

## Output Tokens
Output tokens are tokens that were created by the workflow process itself.  You might need one of these if you want
to create a request for approval, and you want to store the approval request id on the workflow process so you can 
find the status of that request somewhere later in the workflow.  You can create an output token manually as follows:

```php
$workflowProcess = WorkflowProcess::find($workflowProcessId);
$workflowActivity = WorkflowActivity::find($workflowActivityId);
Workflowable::createOutputToken($workflowProcess, $workflowActivity, 'token_key', 'token_value');
