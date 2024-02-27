# Events

## Workflows

| Name                      | Description                                                                                                                                                 |
|---------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------|
| WorkflowActivated         | The workflow has been activated, and will now create new workflow processes on the triggering of the associated workflow event                              |
| WorkflowArchived          | All workflow processes have been wrapped up and we have no further use for this workflow                                                                    |
| WorkflowDeactivated       | We no longer want to create any new workflow processes for this workflow, but we may not have completed all outstanding workflow processes                  |

## Workflow Swaps

| Name                      | Description                                                                                                                                                 |
|---------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------|
| WorkflowSwapCreated       | We have created a new workflow swap in preparation of deprecating all workflow processes belonging to a specific workflow                                   |
| WorkflowSwapDispatched    | We have dispatched the workflow swap in anticipation of it being picked up by the queue for processing                                                      |
| WorkflowSwapScheduled     | We have scheduled the workflow swap to be processed at a date and time in the future                                                                        |
| WorkflowSwapProcessing    | All outstanding dispatched workflow processes have wrapped up their open work and have been returned to a pending state so we can begin performing the swap |
| WorkflowSwapCompleted     | We have finished performing the workflow swap                                                                                                               |

## Workflow Processes

| Name                      | Description                                                                                                                                                 |
|---------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------|
| WorkflowProcessCreated    | Alerts the system that a workflow event has triggered the creation of a new workflow process                                                                |
| WorkflowProcessDispatched | Indicates that a workflow process has been dispatched to the queue for processing                                                                           |
| WorkflowProcessCancelled  | We have decided we no longer want to perform any additional work on a workflow process                                                                      |
| WorkflowProcessPaused     | We have temporarily paused work on a workflow process                                                                                                       |
| WorkflowProcessResumed    | We have removed the temporary pause on a workflow process                                                                                                   |
| WorkflowProcessFailed     | At some point in the workflow process, we have failed to successfully perform the work in a workflow process likely due to an exception                     |
| WorkflowProcessCompleted  | We have successfully completed all work for a workflow process                                                                                              |

### Workflow Process Activities

| Name                      | Description                                                                                            |
|---------------------------|--------------------------------------------------------------------------------------------------------|
| WorkflowActivityStarted   | Indicates that we have begun performing the workflow activity for a specific workflow process          |
| WorkflowActivityCompleted | Indicates that we have successfully completed performing the workflow activity for a workflow process. |
| WorkflowActivityFailed    | Indicates that we have failed to perform a workflow activity, likely due to an exception being thrown  |
