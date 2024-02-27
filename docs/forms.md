# Forms

## Defining Fields

In Workflowable, forms consist of composable form fields that are rendered into JSON for communication with a front-end framework. The backend defines the necessary content for specific workflow conditions, activities, or events, while the front end is responsible for rendering and sending back the contents.

```php
FormManager::make([
    Text::make('Name', 'name')
        ->rules('required|string|min:4'),
    Number::make('Age', 'age')
        ->step(1),
    Select::make('Role', 'role')
        ->options([
            'admin' => 'Admin',
            'author' => 'Author',
        ])
]);
```

## Default Values

By default, every form field can be initialized with a default value.

```php
FormManager::make([
    Text::make('Name', 'name')->default('John Doe')
]);
```

## Field Hydration

There are two ways to hydrate form fields with their current values. The first is to directly fill the form with the value:

```php
FormManager::make([
    Text::make('Name', 'name')
])->fill([
    'name' => 'John Doe'
])

// Result: John Doe
```

The second way allows you to perform data manipulation before setting the value in the form:

```php
FormManager::make([
    Text::make('Name', 'name')
        ->fillUsing(function ($value) {
            return Str::title($value);
        })
])->fill([
    'name' => 'John doe'
])

// Result: John Doe
```

## Customizing For Display

When the data set to the form needs to be displayed differently, you can use the `displayUsing` method to manipulate the value:

```php
FormManager::make([
    Text::make('Age', 'age')
        ->displayUsing(function ($value) {
            return $value . ' Years Old';
        })
])->fill([
    'age' => 21
])

// Result: 21 Years Old
```

## Help Text

You can add help text as part of a tooltip or general display text:

```php
FormManager::make([
    Text::make('Age', 'age')
        ->displayUsing(function ($value) {
            return $value . ' Years Old';
        })
])->fill([
    'age' => 21
])
```

## Extending Fields

To provide maximum flexibility, all fields use the Laravel `Macroable` trait, allowing you to add additional functionality to both your fields and fields created in packages you do not own.

```php
Text::macro('toUpper', function () {
    return $this->displayUsing(function ($value) {
        return Str::upper($value);
    });
});
```
