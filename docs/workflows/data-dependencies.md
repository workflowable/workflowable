# Data Dependencies

**Data dependencies** are used to define dependencies within the structure of your workflows.
It references dependencies between workflow activities/conditions and the workflow process 
input tokens which are required to be provided by the workflow event when creating the workflow process.
We use our workflow activity types and workflow 
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

Assuming you are reliant on the workflow to provide you certain token keys, we would then need to define the 
specific keys that we are dependent on.  We can do this by using the following method:

```php
public function getRequiredWorkflowEventTokenKeys(): array
{
    return [
        'REQUIRED_KEY_HERE'
    ];
}
```

From here you can then test the integrity of your workflow by invoking the command and find out where any unmet 
input token dependencies exist in your workflow event configuration.

```bash
php artisan workflowable:verify-integrity
```

### Adding Integrity Checks To GitHub Actions

```yaml
name: Verify Workflowable Integrity

on:
  push:
    branches: [ "master" ]
  pull_request:
    branches: [ "master" ]

jobs:
  verify-workflowable-integrity:

    runs-on: ubuntu-latest

    steps:
    - uses: shivammathur/setup-php@15c43e89cdef867065b0213be354c2841860869e
      with:
        php-version: '8.2'
    - uses: actions/checkout@v3
    - name: Copy .env
      run: php -r "file_exists('.env') || copy('.env.example', '.env');"
    - name: Install Dependencies
      run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist
    - name: Generate key
      run: php artisan key:generate
    - name: Directory Permissions
      run: chmod -R 777 storage bootstrap/cache
    - name: Create Database
      run: |
        mkdir -p database
        touch database/database.sqlite
    - name: Verify Workflowable Integrity
      id: verify-integrity
      env:
        DB_CONNECTION: sqlite
        DB_DATABASE: database/database.sqlite
      run: |
        php artisan migrate
        php artisan workflowable:scaffold
        php artisan workflowable:verify-integrity
```
