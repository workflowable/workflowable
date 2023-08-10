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
