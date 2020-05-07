<?php

declare(strict_types=1);

namespace Tests\Database;

use Nucleus\Database\Query;
use Nucleus\Database\Selector;
use PHPUnit\Framework\TestCase;
use Tests\Database\Classes\DatabaseTestFactory;

/**
 * Tests the Query class.
 */
class QueryTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        $database = DatabaseTestFactory::database();
        $database->clear();
        $database->addSchema('test', ['field1' => ['type' => 'string']]);
    }

    /**
     * Tests that it is possible to create queries aith empty where clauses.
     *
     * @return void
     */
    public function testEmptyWhereClause(): void
    {
        $query = DatabaseTestFactory::query('test');

        $this->assertSame([
            'action' => null,
            'schema' => 'test',
            'where'  => null,
            'values' => null
        ], $query->toArray());
    }

    /**
     * Tests that it is possible to add a where clause to a query.
     *
     * @return void
     */
    public function testBuildsCondition(): void
    {
        $query = DatabaseTestFactory::query('test');

        $query->where()
            ->eq('field1', 'abc');

        $this->assertSame([
            'action' => null,
            'schema' => 'test',
            'where'  => [
                [
                    'operand'      => null,
                    'not'          => false,
                    'left'         => 'field1',
                    'operator'     => Selector::OPERATOR_EQ,
                    'right'        => 'abc',
                    'subCondition' => null
                ]
            ],
            'values' => null
        ], $query->toArray());
    }

    /**
     * Tests that the appropriate action is set for the different types of queries.
     *
     * @return void
     */
    public function testSetsAction(): void
    {
        $query = DatabaseTestFactory::query('test');

        $query->getAll();
        $this->assertEquals(Query::ACTION_GET, $query->toArray()['action']);

        $query->updateAll([]);
        $this->assertEquals(Query::ACTION_UPDATE, $query->toArray()['action']);

        $query->deleteAll();
        $this->assertEquals(Query::ACTION_DELETE, $query->toArray()['action']);

        $where = $query->where()->eq('field', 123);

        $where->get();
        $this->assertEquals(Query::ACTION_GET, $query->toArray()['action']);

        $where->update([]);
        $this->assertEquals(Query::ACTION_UPDATE, $query->toArray()['action']);

        $where->delete();
        $this->assertEquals(Query::ACTION_DELETE, $query->toArray()['action']);
    }
}
