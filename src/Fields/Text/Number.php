<?php

namespace Workflowable\Workflowable\Fields\Text;

use Illuminate\Support\Traits\Macroable;
use Workflowable\Workflowable\Contracts\Fields\NumberContract;
use Workflowable\Workflowable\Fields\Field;

class Number extends Text implements NumberContract
{
    use Macroable;

    protected string $component = 'number-field';

    /**
     * The step value for the number field
     *
     * @return $this
     */
    public function step(float|int $step): self
    {
        $this->withMetaData([
            'step' => $step,
        ]);

        return $this;
    }
}
