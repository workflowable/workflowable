<?php

namespace Workflowable\Workflowable\Tests\Unit\ParameterConversions;

use Workflowable\Workflowable\ParameterConversions\FloatParameterConversion;
use Workflowable\Workflowable\Tests\TestCase;

class FloatParameterConversionTest extends TestCase
{
    public function test_storing_parameter()
    {
        $value = 123.123;
        $converter = new FloatParameterConversion();
        $converted = $converter->store($value);
        $this->assertIsString($converted);
        $this->assertEquals('123.123', $converted);
    }

    public function test_retrieval_of_parameter()
    {
        $value = '123.123';
        $converter = new FloatParameterConversion();
        $converted = $converter->retrieve($value, 'float');
        $this->assertIsFloat($converted);
        $this->assertEquals(123.123, $converted);
    }

    public function test_getting_parameter_conversion_type()
    {
        $converter = new FloatParameterConversion();
        $this->assertEquals('float', $converter->getParameterConversionType());
    }

    public function test_we_are_eligible_to_prepare_for_storage()
    {
        $value = 123.123;
        $converter = new FloatParameterConversion();
        $this->assertTrue($converter->canPrepareForStorage($value));
    }

    public function test_we_are_eligible_to_retrieve_from_storage()
    {
        $value = '123.123';
        $converter = new FloatParameterConversion();
        $this->assertTrue($converter->canRetrieveFromStorage($value, 'float'));
    }
}
