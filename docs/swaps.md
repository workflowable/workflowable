# Workflow Swaps

A **workflow swap** not only facilitates the replacement of an existing workflow for future instances triggered by an event but also handles the transition of ongoing processes associated with the old workflow. As part of this process, detailed audit logs are maintained to capture the transitions between the original and replacement workflows.

## Purpose and Benefits

The primary purpose of a workflow swap is to improve the performance of an existing workflow. To seamlessly transition ongoing processes to a more effective workflow, mappings are created for each workflow activity associated with the workflow being replaced.

## Mapping Process

During a workflow swap, the system creates mapping records for each workflow activity of the original workflow. These mappings govern the transition of workflow processes and can be customized in two ways:

1. **Specifying a New Activity:** Identify a specific activity in the new workflow to transition to. The system then sets this activity as the last performed activity of the workflow process.

2. **Starting from the Beginning:** Choose not to specify a new activity in the new workflow, causing the process to start from the beginning.

Additionally, the mapping process allows for the optional movement of existing outputted workflow process tokens.

## Invoking Workflow Swaps

Workflow swaps can be scheduled for a future date or requested to be performed instantly. The actual swap execution, however, is deferred until ongoing workflow processes complete their work. This ensures a smooth transition without disrupting the ongoing work or introducing inconsistencies.

## Audit Logging

To maintain transparency and provide a record of each workflow transition, detailed audit logs are generated. These logs include information such as:

- `workflow_swap_id`: Identifier for the specific workflow swap.
- `from_workflow_process_id`: ID of the original workflow process.
- `from_workflow_activity_id`: ID of the original workflow activity.
- `to_workflow_process_id`: ID of the new workflow process.
- `to_workflow_activity_id`: ID of the new workflow activity (nullable for starting from the beginning).
- `created_at`: Timestamp indicating when the log entry was created.
- `updated_at`: Timestamp indicating any updates to the log entry.

This structured logging approach ensures traceability and accountability for each workflow swap, allowing administrators to review the history of transitions and troubleshoot any issues.
