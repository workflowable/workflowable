<?php

namespace Workflowable\Workflowable\Tests\Unit\ParameterConversions;

use Workflowable\Workflowable\ParameterConversions\ArrayParameterConversion;
use Workflowable\Workflowable\Tests\TestCase;

class ArrayParameterConversionTest extends TestCase
{
    public function test_storing_parameter()
    {
        $value = ['foo' => 'bar'];
        $converter = new ArrayParameterConversion();
        $converted = $converter->store($value);
        $this->assertIsString($converted);
        $this->assertEquals(json_encode($value), $converted);
    }

    public function test_retrieval_of_parameter()
    {
        $value = json_encode(['foo' => 'bar']);
        $converter = new ArrayParameterConversion();
        $converted = $converter->retrieve($value, 'array');
        $this->assertIsArray($converted);
        $this->assertEquals(['foo' => 'bar'], $converted);
    }

    public function test_getting_parameter_conversion_type()
    {
        $converter = new ArrayParameterConversion();
        $this->assertEquals('array', $converter->getParameterConversionType());
    }

    public function test_we_are_eligible_to_prepare_for_storage()
    {
        $value = ['foo' => 'bar'];
        $converter = new ArrayParameterConversion();
        $this->assertTrue($converter->canPrepareForStorage($value));
    }

    public function test_we_are_eligible_to_retrieve_from_storage()
    {
        $value = json_encode(['foo' => 'bar']);
        $converter = new ArrayParameterConversion();
        $this->assertTrue($converter->canRetrieveFromStorage($value, 'array'));
    }
}
