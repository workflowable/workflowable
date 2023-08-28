<?php

namespace Workflowable\Workflowable\Fields\Text;

use Illuminate\Support\Traits\Macroable;
use Workflowable\Workflowable\Contracts\Fields\TelephoneContract;

class Telephone extends Text implements TelephoneContract
{
    use Macroable;

    protected string $component = 'telephone-field';
}
