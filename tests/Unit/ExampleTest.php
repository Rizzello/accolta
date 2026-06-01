<?php

namespace Tests\Unit;

use App\Enums\FieldType;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_field_type_has_label(): void
    {
        $this->assertSame('Email', FieldType::Email->getLabel());
    }
}
