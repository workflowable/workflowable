<?php

namespace Workflowable\Workflowable\Fields\Text;

use Illuminate\Support\Traits\Macroable;
use Workflowable\Workflowable\Fields\Field;

class Text extends Field
{
    use Macroable;

    protected string $component = 'text-field';

    /**
     * The minimum number of characters
     *
     * @return $this
     */
    public function min(int $min): self
    {
        $this->withMetaData([
            'min' => $min,
        ]);

        return $this;
    }

    public function max(int $max): self
    {
        $this->withMetaData([
            'max' => $max,
        ]);

        return $this;
    }

    public function placeholder(string $placeholder): self
    {
        $this->withMetaData([
            'placeholder' => $placeholder,
        ]);

        return $this;
    }
}
