<?php

namespace Workflowable\Workflowable\Tests\Unit\ParameterConversions;

use Workflowable\Workflowable\ParameterConversions\StringParameterConversion;
use Workflowable\Workflowable\Tests\TestCase;

class StringParameterConversionTest extends TestCase
{
    public function test_storing_parameter()
    {
        $value = 'string';
        $converter = new StringParameterConversion();
        $converted = $converter->store($value);
        $this->assertIsString($converted);
        $this->assertEquals('string', $converted);
    }

    public function test_retrieval_of_parameter()
    {
        $value = 'string';
        $converter = new StringParameterConversion();
        $converted = $converter->retrieve($value, 'string');
        $this->assertIsString($converted);
        $this->assertEquals('string', $converted);
    }

    public function test_getting_parameter_conversion_type()
    {
        $converter = new StringParameterConversion();
        $this->assertEquals('string', $converter->getParameterConversionType());
    }

    public function test_we_are_eligible_to_prepare_for_storage()
    {
        $value = 'string';
        $converter = new StringParameterConversion();
        $this->assertTrue($converter->canPrepareForStorage($value));
    }

    public function test_we_are_eligible_to_retrieve_from_storage()
    {
        $value = 'string';
        $converter = new StringParameterConversion();
        $this->assertTrue($converter->canRetrieveFromStorage($value, 'string'));
    }
}
