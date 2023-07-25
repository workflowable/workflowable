<?php

namespace Workflowable\Workflowable\Tests\Unit\ParameterConversions;

use Workflowable\Workflowable\ParameterConversions\NullParameterConversion;
use Workflowable\Workflowable\Tests\TestCase;

class NullParameterConversionTest extends TestCase
{
    public function test_storing_parameter()
    {
        $value = null;
        $converter = new NullParameterConversion();
        $converted = $converter->store($value);
        $this->assertNull($converted);
        $this->assertEquals(null, $converted);
    }

    public function test_retrieval_of_parameter()
    {
        $value = null;
        $converter = new NullParameterConversion();
        $converted = $converter->retrieve($value, 'null');
        $this->assertNull($converted);
        $this->assertEquals(null, $converted);
    }

    public function test_getting_parameter_conversion_type()
    {
        $converter = new NullParameterConversion();
        $this->assertEquals('null', $converter->getParameterConversionType());
    }

    public function test_we_are_eligible_to_prepare_for_storage()
    {
        $value = null;
        $converter = new NullParameterConversion();
        $this->assertTrue($converter->canPrepareForStorage($value));
    }

    public function test_we_are_eligible_to_retrieve_from_storage()
    {
        $value = null;
        $converter = new NullParameterConversion();
        $this->assertTrue($converter->canRetrieveFromStorage($value, 'null'));
    }
}
