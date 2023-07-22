<?php

namespace Workflowable\Workflowable\Tests\Unit\Casts;

use Workflowable\Workflowable\Casts\ParameterCast;
use Workflowable\Workflowable\Exceptions\ParameterException;
use Workflowable\Workflowable\Models\WorkflowConfigurationParameter;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class ParameterCastTest extends TestCase
{
    public function test_setting_an_integer()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->set($workflowConfigurationParameter, 'value', 1, $workflowConfigurationParameter->attributesToArray());

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('type', $result);

        $this->assertEquals(1, $result['value']);
        $this->assertEquals('int', $result['type']);
    }

    public function test_setting_a_bool()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->set($workflowConfigurationParameter, 'value', true, $workflowConfigurationParameter->attributesToArray());

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('type', $result);

        $this->assertEquals(true, $result['value']);
        $this->assertEquals('bool', $result['type']);
    }

    public function test_setting_a_float()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->set($workflowConfigurationParameter, 'value', 123.34343, $workflowConfigurationParameter->attributesToArray());

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('type', $result);

        $this->assertEquals(123.34343, $result['value']);
        $this->assertEquals('float', $result['type']);
    }

    public function test_setting_a_string()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->set($workflowConfigurationParameter, 'value', 'test', $workflowConfigurationParameter->attributesToArray());

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('type', $result);

        $this->assertEquals('test', $result['value']);
        $this->assertEquals('string', $result['type']);
    }

    public function test_setting_an_array()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->set($workflowConfigurationParameter, 'value', [1, 2, 3], $workflowConfigurationParameter->attributesToArray());

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('type', $result);

        $this->assertEquals(json_encode([1, 2, 3]), $result['value']);
        $this->assertEquals('array', $result['type']);
    }

    public function test_setting_a_null()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->set($workflowConfigurationParameter, 'value', null, $workflowConfigurationParameter->attributesToArray());

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('type', $result);

        $this->assertEquals(null, $result['value']);
        $this->assertEquals('null', $result['type']);
    }

    public function test_setting_a_model()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->set($workflowConfigurationParameter, 'value', $workflowEvent, $workflowConfigurationParameter->attributesToArray());

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('type', $result);

        $this->assertEquals($workflowEvent->id, $result['value']);
        $this->assertEquals(WorkflowEvent::class, $result['type']);

    }

    public function test_getting_an_integer()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->get($workflowConfigurationParameter, 'value', '1', [
            'type' => 'int',
        ]);

        $this->assertIsInt($result);
        $this->assertEquals(1, $result);
    }

    public function test_getting_a_bool_returns_true()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->get($workflowConfigurationParameter, 'value', 'true', [
            'type' => 'bool',
        ]);

        $this->assertIsBool($result);
        $this->assertEquals(true, $result);
    }

    public function test_getting_a_bool_returns_false()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->get($workflowConfigurationParameter, 'value', 'false', [
            'type' => 'bool',
        ]);

        $this->assertIsBool($result);
        $this->assertEquals(false, $result);
    }

    public function test_getting_a_float()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->get($workflowConfigurationParameter, 'value', '123.123456', [
            'type' => 'float',
        ]);

        $this->assertIsFloat($result);
        $this->assertEquals(123.123456, $result);
    }

    public function test_getting_a_string()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->get($workflowConfigurationParameter, 'value', 'test', [
            'type' => 'string',
        ]);

        $this->assertIsString($result);
        $this->assertEquals('test', $result);
    }

    public function test_getting_an_array()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->get($workflowConfigurationParameter, 'value', json_encode([1, 2, 3]), [
            'type' => 'array',
        ]);

        $this->assertIsArray($result);
        $this->assertEqualsCanonicalizing([1, 2, 3], $result);
    }

    public function test_getting_a_null()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->get($workflowConfigurationParameter, 'value', 'null', [
            'type' => 'null',
        ]);

        $this->assertNull($result);
        $this->assertEqualsCanonicalizing(null, $result);
    }

    public function test_getting_a_model()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();
        $result = $parameterCast->get($workflowConfigurationParameter, 'value', $workflowEvent->id, [
            'type' => WorkflowEvent::class,
        ]);

        $this->assertInstanceOf(WorkflowEvent::class, $result);
        $this->assertEquals($workflowEvent->id, $result->id);
    }

    public function test_handling_with_invalid_type()
    {
        $workflowConfigurationParameter = new WorkflowConfigurationParameter();
        $parameterCast = new ParameterCast();

        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage(ParameterException::unsupportedParameterType('bad_type')->getMessage());
        $result = $parameterCast->get($workflowConfigurationParameter, 'value', 1, [
            'type' => 'bad_type',
        ]);
    }
}
