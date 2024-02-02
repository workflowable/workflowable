# Forms

## Defining Fields

In Workflowable, forms are made up of composable form fields, that end up being rendered down into JSON to be
communicated to a front end framework for display.  The idea here is that nobody is going to be able to know more about
what is needed for the contents of a specific workflow condition, activity, or event better than the backend, whereas
the front end just need to render it out, and send the contents back.

```php
FormManager::make([
    Text::make('Name', 'name')
        ->rules('required|string|min:4'),
    Number::make('Age', 'age')
        ->step(1),
    Select::make('Role', 'role')
        ->options([
            'admin' => 'Admin',
            'author' => 'author',
        ])
]);
```

## Default Values

By default, every form field in the 
```php
FormManager::make([
    Text::make('Name', 'name')->default('John Doe')
]);
```

## Field Hydration

We have two different ways of hydrating our form fields with their current values.  The first, is to just straight fill
the form with the value directly.  You can do this like so:

```php
FormManager::make([
    Text::make('Name', 'name')
])->fill([
    'name' => 'John Doe'
])

// John Doe
```

Our second was way, allows you to perform a data manipulation against it before allowing it to be set as a value in the
form, which can be done as follows:

```php
FormManager::make([
    Text::make('Name', 'name')
        ->fillUsing(function ($value) {
            return Str::title($value);
        })
])->fill([
    'name' => 'John doe'
])

// John Doe
```

## Customizing For Display

When the data being set to the form isn't necessarily how you want to display the data, you can invoke the `displayUsing`
method to manipulate the value of the form field into way you desire to present it to the user.

```php
FormManager::make([
    Text::make('Age', 'age')
        ->displayUsing(function ($value) {
            return $value . 'Years Old';
        })
])->fill([
    'age' => 21
])

// 21 Years Old
```

## Help Text


## Extending Fields

## Field Types
