<?php

namespace Workflowable\Workflowable\Tests\Unit\ParameterConversions;

use Workflowable\Workflowable\Exceptions\ParameterException;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\ParameterConversions\ModelParameterConversion;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class ModelParameterConversionTest extends TestCase
{
    public function test_storing_parameter()
    {
        $value = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        $converter = new ModelParameterConversion();
        $converted = $converter->store($value);
        $this->assertIsString($converted);
        $this->assertEquals(''.$value->id, $converted);
        $this->assertEquals('model:'.WorkflowEvent::class, $converter->getParameterConversionType());
    }

    public function test_retrieval_of_parameter()
    {
        $value = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        $converter = new ModelParameterConversion();
        $converted = $converter->retrieve($value->id, 'model:'.WorkflowEvent::class);
        $this->assertInstanceOf(WorkflowEvent::class, $converted);
        $this->assertEquals($value->id, $converted->id);
    }

    public function test_getting_parameter_conversion_type()
    {
        $converter = new ModelParameterConversion();
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Unable to get parameter conversion type.');
        $converter->getParameterConversionType();
    }

    public function test_we_are_eligible_to_prepare_for_storage()
    {
        $value = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        $converter = new ModelParameterConversion();
        $this->assertTrue($converter->canPrepareForStorage($value));
    }

    public function test_we_are_eligible_to_retrieve_from_storage()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $converter = new ModelParameterConversion();
        $this->assertTrue($converter->canRetrieveFromStorage($workflowEvent->id, 'model:'.WorkflowEvent::class));
    }
}
