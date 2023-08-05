<?php

namespace Workflowable\Workflowable\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatesInputTokens
{
    protected array $tokens = [];

    public function __construct(array $tokens = [])
    {
        $this->tokens = $tokens;
    }

    abstract public function getRules(): array;

    /**
     * Evaluates the tokens against the rules.
     */
    public function hasValidTokens(): bool
    {
        $validator = Validator::make($this->tokens, $this->getRules());

        return $validator->passes();
    }

    public function getTokens(): array
    {
        return $this->tokens;
    }
}
