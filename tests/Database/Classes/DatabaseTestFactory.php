<?php

declare(strict_types=1);

namespace Tests\Database\Classes;

use Nucleus\Database\Collection;
use Nucleus\Database\Database;
use Nucleus\Database\Databases\MockDatabase;
use Nucleus\Database\Query;
use Nucleus\Database\Record;
use Nucleus\Database\RecordSchema;
use Nucleus\Database\Selector;

/**
 * Class used to instancitate mock objects to test the database module.
 */
class DatabaseTestFactory
{
    /**
     * The instance of the database.
     *
     * @var Database
     */
    private static $database = null;

    /**
     * Creates a new mock database.
     *
     * @return Database A new instance of database.
     */
    public static function database(): Database
    {
        if (self::$database === null) {
            self::$database = new MockDatabase();
        }

        return self::$database;
    }

    /**
     * Creates a new query.
     *
     * @param string $schema The schema on which the query must be
     * executed.
     * @return Query A query.
     */
    public static function query(string $schema): Query
    {
        return self::database()->query($schema);
    }

    /**
     * Creates a new selector.
     *
     * @param string $schema The name of the schema associated to the
     * selector.
     * @return Selector A new selector.
     */
    public static function selector(string $schema): Selector
    {
        return self::query($schema)->where();
    }
}
