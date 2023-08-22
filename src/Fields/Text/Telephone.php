<?php

namespace Workflowable\Workflowable\Fields\Text;

use Illuminate\Support\Traits\Macroable;

class Telephone extends Text
{
    use Macroable;

    protected string $component = 'telephone-field';
}
