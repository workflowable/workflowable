# Data Dependencies

**Data dependencies** are used to define dependencies within the structure of your workflows.
It references dependencies between workflow steps/conditions and the workflow run 
parameters which are defined in the workflow event definition.  We use our workflow step types and workflow 
condition types to help map out these dependencies through a couple of methods on them.  First, let's take a look at:

```php
public function getWorkflowEventAliases(): array
{
    return [
        'workflow-event-alias'
    ];
}
```

This helps us map out the specific workflow event aliases that we are dependent on.  This is useful in helping us speed
up the process of finding the workflow event classes that we are dependent on.

Assuming you are reliant on the workflow to provide you certain parameters, we would then need to define the 
specific keys that we are dependent on.  We can do this by using the following method:

```php
public function getRequiredWorkflowEventKeys(): array
{
    return [
        'REQUIRED_KEY_HERE'
    ];
}
```

From here you can then test the integrity of your workflow by invoking the command and find out where any unmet 
dependencies exist in your workflow event configuration.

```bash
php artisan workflowable:verify-integrity
```
