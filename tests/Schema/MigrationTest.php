<?php

declare(strict_types=1);

namespace Tests\Schema;

use Nucleus\Schema\Exceptions\MigrationErrorException;
use Nucleus\Schema\Migration;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Migration class.
 */
class MigrationTest extends TestCase
{
    /**
     * Tests the 'Create' operation.
     *
     * @return void
     */
    public function testCreate(): void
    {
        // Test valid schema arrays.
        $migration = new Migration();
        $res = $migration->create(['field' => ['type' => 'number']]);
        $this->assertInstanceOf(Migration::class, $res);

        // Test that the action is correctly set.
        $this->assertSame([
            'action'     => Migration::ACTION_CREATE,
            'parameters' => null,
            'schema'     => [
                'field' => [
                    'type'     => 'number',
                    'required' => false,
                    'list'     => false,
                    'hidden'   => false
                ]
            ],
            'next'       => null
        ], $migration->toArray());

        $this->assertNotNull($migration->schema());
        $this->assertEquals(Migration::ACTION_CREATE, $migration->toArray()['action']);

        // Test invalid schema arrays.
        try {
            $migration = $migration->create(['field' => []]);
            $this->fail('An MigrationErrorException was expected to be thrown.');
        } catch (MigrationErrorException $e) {
            // Nothing to do...
        }
    }

    /**
     * Tests the 'Delete' operation.
     *
     * @return void
     */
    public function testDelete(): void
    {
        // Delete an undefined schema
        try {
            $migration = new Migration();
            $migration->delete();
            $this->fail('An MigrationErrorException was expected to be thrown.');
        } catch (MigrationErrorException $e) {
            // Nothing to do...
        }

        // Test valid cases.
        $migration = (new Migration())->create([]);
        $res = $migration->delete();

        $this->assertNull($res);
        $this->assertEquals(Migration::ACTION_DELETE, $migration->toArray()['action']);
    }

    /**
     * Tests the 'Add field' operation.
     *
     * @return void
     */
    public function testAddField(): void
    {
        // Add a field to an undefined schema
        try {
            $migration = new Migration();
            $migration->addField('myField', ['type' => 'number']);
            $this->fail('An MigrationErrorException was expected to be thrown.');
        } catch (MigrationErrorException $e) {
            // Nothing to do...
        }

        // Add an invalid field
        try {
            $migration = new Migration();
            $migration->create([])->addField('myField', []);
            $this->fail('An MigrationErrorException was expected to be thrown.');
        } catch (MigrationErrorException $e) {
            // Nothing to do...
        }

        // Valid case
        $migration = (new Migration())->create([]);
        $migration->addField('myField', ['type' => 'number']);

        $this->assertSame([
            'action'     => Migration::ACTION_FIELD_ADD,
            'parameters' => [
                'name'  => 'myField',
                'array' => ['type' => 'number']
            ],
            'schema'     => [
                'myField' => [
                    'type'     => 'number',
                    'required' => false,
                    'list'     => false,
                    'hidden'   => false
                ]
            ],
            'next'       => null
        ], $migration->toArray());

        $this->assertNotNull($migration->schema());
    }

    /**
     * Tests the 'Remove field' operation.
     *
     * @return void
     */
    public function testRemoveField(): void
    {
        // Remove a field to an undefined schema
        try {
            $migration = new Migration();
            $migration->removeField('myField');
            $this->fail('An MigrationErrorException was expected to be thrown.');
        } catch (MigrationErrorException $e) {
            // Nothing to do...
        }

        // Remove an undefined field
        try {
            $migration = new Migration();
            $migration->create([])->removeField('myField');
            $this->fail('An MigrationErrorException was expected to be thrown.');
        } catch (MigrationErrorException $e) {
            // Nothing to do...
        }

        // Valid case
        $migration = new Migration();
        $migration = $migration->create(['myField' => ['type' => 'number']]);
        $migration->removeField('myField');

        $this->assertSame([
            'action'     => Migration::ACTION_FIELD_REMOVE,
            'parameters' => ['name' => 'myField'],
            'schema'     => [],
            'next'       => null
        ], $migration->toArray());

        $this->assertNotNull($migration->schema());
    }

    /**
     * Tests the 'Modify field' operation.
     *
     * @return void
     */
    public function testModifyField(): void
    {
        // Modify a field to an undefined schema
        try {
            $migration = new Migration();
            $migration->modifyField('myField', ['list' => true]);
            $this->fail('An MigrationErrorException was expected to be thrown.');
        } catch (MigrationErrorException $e) {
            // Nothing to do...
        }

        // Modify an undefined field
        try {
            $migration = new Migration();
            $migration->create([])->modifyField('myField', ['list' => true]);
            $this->fail('An MigrationErrorException was expected to be thrown.');
        } catch (MigrationErrorException $e) {
            // Nothing to do...
        }

        // Set invalid values
        try {
            $migration = new Migration();
            $migration->create([])->modifyField('myField', ['list' => 'abc']);
            $this->fail('An MigrationErrorException was expected to be thrown.');
        } catch (MigrationErrorException $e) {
            // Nothing to do...
        }

        // Valid case
        $migration = new Migration();
        $migration = $migration->create(['myField' => ['type' => 'number']]);
        $migration->modifyField('myField', ['list' => true]);

        $this->assertSame([
            'action'     => Migration::ACTION_FIELD_MODIFY,
            'parameters' => [
                'name'   => 'myField',
                'values' => ['list' => true]
            ],
            'schema'     => [
                'myField' => [
                    'type'     => 'number',
                    'required' => false,
                    'list'     => true,
                    'hidden'   => false
                ]
            ],
            'next'       => null
        ], $migration->toArray());

        $this->assertNotNull($migration->schema());
    }
}
