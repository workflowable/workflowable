# Workflow Transitions

A **workflow transition** refers to the movement or progression of a workflow from one state or step to another. It
represents the path or connection between two workflow activities, indicating the flow of work and the logical sequence of actions within a workflow.

### How can I add conditions to determine what transitions are eligible to be performed?

You can do this through one or more **workflow conditions**. Like workflow activities, workflow conditions have a workflow
condition type that validates, defines dependencies, and handles executing the code to check whether a specific condition is met.

### Can I order the transitions and conditions?

Yes, every transition and condition has an **ordinal value** that determines the order in which they are
evaluated. The lower the ordinal value, the higher the priority. The default ordinal value is 0.
