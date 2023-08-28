<?php

namespace Workflowable\Workflowable\Fields\Selection;

use Illuminate\Support\Traits\Macroable;
use Workflowable\Workflowable\Contracts\Fields\SelectContract;
use Workflowable\Workflowable\Fields\Field;

class MultiSelect extends Select implements SelectContract
{
    use Macroable;

    /**
     * Defines the options for a select field
     *
     * @return $this
     */
    public function options(array $options): self
    {
        $this->withMetaData([
            'options' => $options,
        ]);

        return $this;
    }
}
