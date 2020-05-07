<?php

declare(strict_types=1);

namespace Tests\Database;

use Nucleus\Database\Exceptions\DatabaseQueryException;
use PHPUnit\Framework\TestCase;
use Tests\Database\Classes\DatabaseTestFactory;

/**
 * Tests the Database class.
 */
class DatabaseTest extends TestCase
{
    /**
     * Tests the 'Create' operation.
     *
     * @return void
     */
    public function testCreate()
    {
        // Setup the database.
        $database = DatabaseTestFactory::database();
        $database->clear();
        $database->addSchema('user', [
            'name'     => ['type' => 'string', 'required' => true],
            'password' => ['type' => 'string', 'required' => true]
        ]);

        // The database must be empty.
        $records = $database->query('user')->getAll();
        $this->assertEmpty($records);

        // Insert one record.
        $database->query('user')->create([
            'name' => 'myName',
            'password' => 'myPswd'
        ]);
        $records = $database->query('user')->getAll();

        $this->assertCount(1, $records);
        $this->assertSame('myName', $records[0]['name']);
        $this->assertSame('myPswd', $records[0]['password']);

        // Insert a second record.
        $database->query('user')->create([
            'name' => 'myName2',
            'password' => 'myPswd2'
        ]);
        $records = $database->query('user')->getAll();

        $this->assertCount(2, $records);
        $this->assertSame('myName', $records[0]['name']);
        $this->assertSame('myPswd', $records[0]['password']);
        $this->assertSame('myName2', $records[1]['name']);
        $this->assertSame('myPswd2', $records[1]['password']);

        // Insert an invalid record
        try {
            $database->query('user')->create(['name' => 'myName2']);
            $this->fail('A DatabaseQueryException was expected.');
        } catch (DatabaseQueryException $e) {
            // Nothing to do...
        }
    }

    /**
     * Tests the 'Get' operation.
     *
     * @return void
     */
    public function testGet(): void
    {
        // Setup the database.
        $database = DatabaseTestFactory::database();
        $database->clear();
        $database->addSchema('user', [
            'name' => ['type' => 'string', 'required' => true],
            'age'  => ['type' => 'number']
        ]);

        // Add records
        $database->query('user')->create(['name' => 'u1', 'age' => 22]);
        $database->query('user')->create(['name' => 'u2', 'age' => 35]);
        $database->query('user')->create(['name' => 'u3', 'age' => 15]);
        $database->query('user')->create(['name' => 'u4', 'age' => 62]);
        $database->query('user')->create(['name' => 'u5', 'age' => 25]);
        $database->query('user')->create(['name' => 'u6', 'age' => null]);

        // Get all
        $records = $database->query('user')->getAll();
        $this->assertCount(6, $records);

        // Get zero
        $records = $database->query('user')
            ->where()->eq('name', 'u1')->andLt('age', 12)
            ->get();

        $this->assertCount(0, $records);

        // Get one record
        $records = $database->query('user')
            ->where()->eq('name', 'u1')
            ->get();

        $this->assertCount(1, $records);

        // Get multiple records
        $records = $database->query('user')
            ->where()->gt('age', '30')
            ->get();

        $this->assertCount(2, $records);
    }

    public function testUpdate(): void
    {
        // Setup the database.
        $database = DatabaseTestFactory::database();
        $database->clear();
        $database->addSchema('user', [
            'name' => ['type' => 'string', 'required' => true],
            'age'  => ['type' => 'number']
        ]);

        // Add records
        $database->query('user')->create(['name' => 'u1', 'age' => 22]);
        $database->query('user')->create(['name' => 'u2', 'age' => 35]);
        $database->query('user')->create(['name' => 'u3', 'age' => 15]);
        $database->query('user')->create(['name' => 'u4', 'age' => 62]);
        $database->query('user')->create(['name' => 'u5', 'age' => 25]);
        $database->query('user')->create(['name' => 'u6', 'age' => null]);

        // Update one record
        $database->query('user')
            ->where()->eq('name', 'u1')
            ->update(['age' => 11]);

        $records = $database->query('user')
            ->where()->eq('name', 'u1')
            ->get();

        $this->assertCount(1, $records);
        $this->assertEquals(11, $records[0]['age']);

        // Update multiple records
        $records = $database->query('user')
            ->where()->gt('age', '30')
            ->get();

        $this->assertCount(2, $records);

        $database->query('user')
            ->where()->gt('age', '30')
            ->update(['age' => 11]);

        $records = $database->query('user')
            ->where()->gt('age', '30')
            ->get();

        $this->assertCount(0, $records);
    }

    /**
     * Tests the 'Delete' operation.
     *
     * @return void
     */
    public function testDelete(): void
    {
        // Setup the database.
        $database = DatabaseTestFactory::database();
        $database->clear();
        $database->addSchema('user', [
            'name' => ['type' => 'string', 'required' => true],
            'age'  => ['type' => 'number']
        ]);

        // Add records
        $database->query('user')->create(['name' => 'u1', 'age' => 22]);
        $database->query('user')->create(['name' => 'u2', 'age' => 35]);
        $database->query('user')->create(['name' => 'u3', 'age' => 15]);
        $database->query('user')->create(['name' => 'u4', 'age' => 62]);
        $database->query('user')->create(['name' => 'u5', 'age' => 25]);
        $database->query('user')->create(['name' => 'u6', 'age' => null]);

        $records = $database->query('user')->getAll();
        $this->assertCount(6, $records);

        // Attempt to delete a record that doesn't exist
        $database->query('user')
            ->where()->eq('name', 'u1')->andLt('age', 12)
            ->delete();

        $records = $database->query('user')->getAll();
        $this->assertCount(6, $records);

        // Delete one record
        $records = $database->query('user')->where()->eq('name', 'u1')->get();
        $this->assertCount(1, $records);

        $database->query('user')
            ->where()->eq('name', 'u1')
            ->delete();

        $records = $database->query('user')->getAll();
        $this->assertCount(5, $records);

        $records = $database->query('user')->where()->eq('name', 'u1')->get();
        $this->assertCount(0, $records);

        // Delete multiple records
        $database->query('user')
            ->where()->gt('age', 30)
            ->delete();

        $records = $database->query('user')->getAll();
        $this->assertCount(3, $records);

        $records = $database->query('user')
            ->where()->eq('name', 'u2')->orEq('name', 'u4')
            ->get();

        $this->assertCount(0, $records);
    }
}
