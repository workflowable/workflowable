<?php

namespace Workflowable\Workflowable\Tests\Unit\ParameterConversions;

use Workflowable\Workflowable\ParameterConversions\BooleanParameterConversion;
use Workflowable\Workflowable\Tests\TestCase;

class BooleanParameterConversionTest extends TestCase
{
    public function test_storing_parameter()
    {
        $value = true;
        $converter = new BooleanParameterConversion();
        $converted = $converter->store($value);
        $this->assertIsString($converted);
        $this->assertEquals('1', $converted);
    }

    public function test_retrieval_of_parameter()
    {
        $value = '1';
        $converter = new BooleanParameterConversion();
        $converted = $converter->retrieve($value, 'boolean');
        $this->assertIsBool($converted);
        $this->assertEquals(true, $converted);
    }

    public function test_getting_parameter_conversion_type()
    {
        $converter = new BooleanParameterConversion();
        $this->assertEquals('boolean', $converter->getParameterConversionType());
    }

    public function test_we_are_eligible_to_prepare_for_storage()
    {
        $value = true;
        $converter = new BooleanParameterConversion();
        $this->assertTrue($converter->canPrepareForStorage($value));
    }

    public function test_we_are_eligible_to_retrieve_from_storage()
    {
        $value = '1';
        $converter = new BooleanParameterConversion();
        $this->assertTrue($converter->canRetrieveFromStorage($value, 'boolean'));
    }
}
