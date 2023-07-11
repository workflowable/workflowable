# Commands

| Command                               | Description                                                                                                                |
|---------------------------------------|----------------------------------------------------------------------------------------------------------------------------|
| `make:workflow-condition-type {name}` | Creates a new workflow condition type class.                                                                               |
| `make:workflow-event {name}`          | Creates a new workflow event class.                                                                                        |
| `make:workflow-step-type {name}`      | Creates a new workflow step type class.                                                                                    |
| `workflowable:process-runs`           | Looks for workflow runs that have reached their next run time and dispatches a WorkflowRunnerJob for each.                 |
| `workflowable:verify-integrity`       | Verifies that the workflow condition types and workflow step types will be provided all data needed by the workflow event. |
| `workflowable:scaffold`               | Can be used upon deploy to ensure that all workflow events, conditions and actions are registered.                         |
