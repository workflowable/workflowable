<?php

namespace Workflowable\Workflowable\Tests\Unit\ParameterConversions;

use Illuminate\Support\Carbon;
use Workflowable\Workflowable\ParameterConversions\DateTimeParameterConversion;
use Workflowable\Workflowable\Tests\TestCase;

class DateParameterConversionTest extends TestCase
{
    public function test_storing_parameter()
    {
        $value = Carbon::parse('2020-01-01');
        $converter = new DateTimeParameterConversion();
        $converted = $converter->store($value);
        $this->assertIsString($converted);
        $this->assertEquals('2020-01-01T00:00:00+00:00', $converted);
    }

    public function test_retrieval_of_parameter()
    {
        $value = '2020-01-01';
        $converter = new DateTimeParameterConversion();
        $converted = $converter->retrieve($value, 'datetime');
        $this->assertInstanceOf(Carbon::class, $converted);
        $this->assertEquals(Carbon::parse($value), $converted);
    }

    public function test_getting_parameter_conversion_type()
    {
        $converter = new DateTimeParameterConversion();
        $this->assertEquals('datetime', $converter->getParameterConversionType());
    }

    public function test_we_are_eligible_to_prepare_for_storage()
    {
        $value = Carbon::parse('2020-01-01');
        $converter = new DateTimeParameterConversion();
        $this->assertTrue($converter->canPrepareForStorage($value));
    }

    public function test_we_are_eligible_to_retrieve_from_storage()
    {
        $value = '2020-01-01';
        $converter = new DateTimeParameterConversion();
        $this->assertTrue($converter->canRetrieveFromStorage($value, 'datetime'));
    }
}
