<?php

namespace Workflowable\Workflowable\Contracts\Fields;

interface NumberContract extends TextContract
{
    /**
     * The step value for the number field
     *
     * @return $this
     */
    public function step(float|int $step): self;
}
