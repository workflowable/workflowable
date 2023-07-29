<?php

namespace Workflowable\Workflowable\Tests\Unit\ParameterConversions;

use Workflowable\Workflowable\ParameterConversions\IntegerParameterConversion;
use Workflowable\Workflowable\Tests\TestCase;

class IntegerParameterConversionTest extends TestCase
{
    public function test_storing_parameter()
    {
        $value = 123;
        $converter = new IntegerParameterConversion();
        $converted = $converter->store($value);
        $this->assertIsString($converted);
        $this->assertEquals('123', $converted);
    }

    public function test_retrieval_of_parameter()
    {
        $value = '123';
        $converter = new IntegerParameterConversion();
        $converted = $converter->retrieve($value, 'integer');
        $this->assertIsInt($converted);
        $this->assertEquals(123, $converted);
    }

    public function test_getting_parameter_conversion_type()
    {
        $converter = new IntegerParameterConversion();
        $this->assertEquals('integer', $converter->getParameterConversionType());
    }

    public function test_we_are_eligible_to_prepare_for_storage()
    {
        $value = 123;
        $converter = new IntegerParameterConversion();
        $this->assertTrue($converter->canPrepareForStorage($value));
    }

    public function test_we_are_eligible_to_retrieve_from_storage()
    {
        $value = '123';
        $converter = new IntegerParameterConversion();
        $this->assertTrue($converter->canRetrieveFromStorage($value, 'integer'));
    }
}
