# Workflow Swaps

A workflow swap exists in the event that you do not merely want to replace an active workflow with a new workflow 
for upcoming instances in which you will trigger an event, but rather, also replace existing workflow processes with 
new processes belonging to the workflow we are attempting to active.

## How is this useful?

Let's say you have enrolled a user into a series of follow-up emails which are defined for a workflow, and that workflow
is under performing.  Rather than allowing those workflow processes to continue down their pre-defined actions, you 
can create a new workflow that you believe will perform better.  Once that workflow is created, you can then create 
mappings from the existing workflow's last completed activity, to a new one of your choice thus allowing you to not 
have to continue work on an under performing workflow.

## How do mappings work?

When creating a workflow swap, we will automatically go and create a map record for each of the workflow activities 
that belong to the workflow we want to remove.  From there, you can:

- Identify a specific activity on the new workflow you want to convert it to, in which case, we will set that 
  activity as the last performed activity of the workflow process.
- Not specify a new activity on the new workflow, which will make it start from the beginning

As part of the mapping process, we support the ability to optionally move existing outputted workflow process tokens 
so that they can still be used in the event that you need them.

## How Are Workflow Swaps Invoked?

Workflow swaps can happen in two different ways.  They can be scheduled, in the event that you want them to run at a 
future date, or you can request that they be performed immediately.  It should be noted though that just because it 
is scheduled for a specific time OR designated to run immediately, we do not immediately execute the swap.  Rather 
we wait for all workflow processes to complete any work they are actively working on, while preventing any new 
workflow processes from being dispatched to the queue, thus ensuring a seamless transition of workflow processes.

## FAQ
- What workflow processes will be impacted?
  - We will look for any workflow processes defined by the active scope that match the originating workflow's primary 
  key.
- What will happen to the originating workflow?
  - We will mark it as cancelled.
- What happens to workflow processes that are running?
  - 

