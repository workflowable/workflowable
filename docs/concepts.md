# Concepts

## Workflow Events

A **workflow event** refers to a trigger that initiates or influences a workflow process. It represents an event that
leads to the execution of workflow steps, the progression of work, or the activation of certain workflow steps.

Workflow events can be internal or external to the system or application hosting the workflow. They can be triggered by system events, user actions, time-based events, data changes, or any other event that requires the workflow engine to respond and execute the appropriate workflow steps.

## Workflows

**Workflows** are the definition of a process or a series of steps that need to be performed to complete a process or
achieve a desired outcome. They can be configured to be as simple or complex as needed through the usage of workflow
steps, transitions and conditions.

## Workflow Priorities

**Workflow priority** refers to the relative importance or urgency of a workflow. It is used to determine the order in
which workflow runs are executed when multiple workflows are triggered at the same time.  Right now, the priority is
a simple integer value, where the greater the value the greater the priority.  In the event that two workflows have
the same priority, the workflow that was created first will be executed first.


## Workflow Steps

A **workflow step** represents a specific action or unit of work within a workflow. It is a fundamental building block 
of a workflow, defining the individual actions or operations that need to be performed to complete a process or achieve a desired outcome.

### How do I define what a workflow step should be doing?

The definition of what a workflow step should perform is called a **workflow step type**. This data structure should be 
responsible for validating your parameters and identifying data dependencies in the workflow run parameters.

## Workflow Transitions

A **workflow transition** refers to the movement or progression of a workflow from one state or step to another. It 
represents the path or connection between two workflow steps, indicating the flow of work and the logical sequence of actions within a workflow.

### How can I add conditions to determine what transitions are eligible to be performed?

You can do this through one or more **workflow conditions**. Like workflow steps, workflow conditions have a workflow 
condition type that validates, defines dependencies, and handles executing the code to check whether a specific condition is met.

### Can I order the transitions and conditions?

Yes, every transition and condition has an **ordinal value** that determines the order in which they are
evaluated. The lower the ordinal value, the higher the priority. The default ordinal value is 0.


## Workflow Runs

**Workflow runs** represent the execution of a workflow. They are the instances of a workflow that are created when a 
workflow is triggered or initiated. They contain the data and information required to execute the workflow steps and progress the workflow through its various states.
