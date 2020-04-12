<?php

declare(strict_types=1);

namespace Tests\Schema;

use Nucleus\Schema\Exceptions\InvalidValueException;
use Nucleus\Schema\Schema;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    /**
     * Tests that the schema accepts valid values.
     *
     * @return void
     */
    public function testAcceptsValidValues(): void
    {
        $schema = new Schema([
            'number1'  => ['type' => 'number',  'required' => true],
            'number2'  => ['type' => 'number',  'required' => true],
            'string1'  => ['type' => 'string',  'required' => true],
            'string2'  => ['type' => 'string',  'required' => true],
            'string3'  => ['type' => 'string',  'required' => true],
            'string4'  => ['type' => 'string',  'required' => true],
            'boolean1' => ['type' => 'boolean', 'required' => true],
            'boolean2' => ['type' => 'boolean', 'required' => true]
        ]);

        $valid = $schema->filter([
            'number1'   => '1',
            'number2'   => '1.5',
            'string1'   => 'abc',
            'string2'   => 1,
            'string3'   => 1.5,
            'string4'   => true,
            'boolean1'  => 'true',
            'boolean2'  => true,
        ]);

        $this->assertSame(1, $valid['number1']);
        $this->assertSame(1.5, $valid['number2']);
        $this->assertSame('abc', $valid['string1']);
        $this->assertSame('1', $valid['string2']);
        $this->assertSame('1.5', $valid['string3']);
        $this->assertSame('true', $valid['string4']);
        $this->assertSame(true, $valid['boolean1']);
        $this->assertSame(true, $valid['boolean2']);
    }

    /**
     * Tests that schemas reject invalid values.
     *
     * @return void
     */
    public function testRejectsInvalidValues(): void
    {
        $this->assertRejectsArray(['val' => ['type' => 'number']], ['val' => true]);
        $this->assertRejectsArray(['val' => ['type' => 'number']], ['val' => '']);
        $this->assertRejectsArray(['val' => ['type' => 'boolean']], ['val' => 123]);
        $this->assertRejectsArray(['val' => ['type' => 'boolean']], ['val' => '']);
    }

    /**
     * Tests that the schema rejects arrays with missing fields.
     *
     * @return void
     */
    public function testDetectsMissingField(): void
    {
        $this->assertRejectsArray(['val' => ['type' => 'number', 'required' => true]], []);
    }

    /**
     * Tests that the schema removes fields that are undefined.
     *
     * @return void
     */
    public function testRemovesUndefinedFields(): void
    {
        $schema = new Schema(['val' => ['type' => 'number']]);
        $filtered = $schema->filter(['foo' => 45]);
        $this->assertArrayNotHasKey('foo', $filtered);
    }

    /**
     * Tests that the schema accepts arrays with missing optional values, and
     * that it fills the missing values with null.
     *
     * @return void
     */
    public function testAcceptsMissingOptionalValues():void
    {
        $schema = new Schema(['val' => ['type' => 'number']]);
        $filtered = $schema->filter([]);
        $this->assertSame(['val' => null], $filtered);
    }

    /**
     * Asserts that a schema rejects a given array.
     *
     * @param array $schema The array representaion of the schema.
     * @param array $array The array.
     * @return void
     */
    private function assertRejectsArray(array $schema, array $array): void
    {
        $this->expectException(InvalidValueException::class);
        (new Schema($schema))->filter($array);
    }
}
