<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Traits;

use Workflowable\WorkflowEngine\Tests\TestCase;
use Workflowable\WorkflowEngine\Traits\ValidatesParameters;

class ValidatesWorkflowParametersTest extends TestCase
{
    public function test_it_can_validate_workflow_parameters(): void
    {
        $class = new class
        {
            use ValidatesParameters;

            public function __construct()
            {
                $this->parameters = [
                    'test' => 'test',
                ];
            }

            public function getRules(): array
            {
                return [
                    'test' => 'required|string|min:3',
                ];
            }
        };

        $this->assertTrue($class->hasValidParameters());

        $invalid = new class
        {
            use ValidatesParameters;

            public function __construct()
            {
                $this->parameters = [
                    'test' => 'test',
                ];
            }

            public function getRules(): array
            {
                return [
                    'test' => 'required|int',
                ];
            }
        };

        $this->assertFalse($invalid->hasValidParameters());
    }

    public function test_it_can_get_the_workflow_parameters(): void
    {
        $class = new class
        {
            use ValidatesParameters;

            public function __construct()
            {
                $this->parameters = [
                    'test' => 'test',
                ];
            }

            public function getRules(): array
            {
                return [
                    'test' => 'required|int',
                ];
            }
        };

        $this->assertEquals(['test' => 'test'], $class->getParameters());
    }
}
