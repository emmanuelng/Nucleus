<?php

declare(strict_types=1);

namespace Nucleus\Database;

use Nucleus\Database\Exceptions\DatabaseInternalException;
use Nucleus\Schema\Migration;
use Nucleus\Schema\Schema;

/**
 * Class representing a database. A database must be able to execute queries,
 * and evolve through migrations. It is responsible for storing the last
 * migration executed for each schema it supports.
 */
abstract class Database
{
    /**
     * Array containing the last executed migration of each supported schema.
     *
     * @var array
     */
    private $migrations = [];

    /**
     * Executes a query.
     *
     * @param Query $query The query to execute.
     * @return mixed The result of the query
     */
    public abstract function executeQuery(Query $query);

    /**
     * Executes a migration.
     *
     * @param string $schema The name of the schema concerned by the migration.
     * @param Migration $migration The migration to execute.
     * @return void
     */
    protected abstract function executeMigration(string $schema, Migration $migration): void;

    /**
     * Adds a schema to the database.
     *
     * @param string $name The name of the schema.
     * @param array $array The array representation of the schema.
     * @return void
     */
    public function addSchema(string $name, array $array): void
    {
        if (array_key_exists($name, $this->migrations)) {
            $msg = "Schema $name already exists";
            throw new DatabaseInternalException($msg);
        }

        $migration = new Migration();
        $migration->create($array);

        $this->executeMigration($name, $migration);
        $this->migrations[$name] = $migration;
    }

    /**
     * Returns a schema. The returned schema is in the state it was in after
     * the last call to the 'update' method. In other words, migrations that
     * have not been executed are not taken into account.
     *
     * @param string $name The name of the schema.
     * @return Schema|null The schema, or null if it doesn't exist.
     */
    public function getSchema(string $name): ?Schema
    {
        if (!isset($this->migrations[$name])) {
            return null;
        }

        return $this->migrations[$name]->schema();
    }

    /**
     * Starts a query on a schema.
     *
     * @param string $schema The schema that is queried.
     * @return Query A new query.
     */
    public function query(string $schema): Query
    {
        if (!isset($this->migrations[$schema])) {
            $msg = "The schema $schema does not exist or is not supported.";
            throw new DatabaseInternalException($msg);
        }

        return new Query($this, $schema);
    }

    /**
     * Returns the next migration to be run for a schema. Note that adding
     * migrations will not directly update the database. You must call the
     * "update" method to do so.
     *
     * @param string $schema The name of the schema.
     * @return Migration|null The migration, or null if the schema is not
     * supported by the schema.
     */
    public function migrate(string $schema): ?Migration
    {
        if (!isset($this->migrations[$schema])) {
            return null;
        }

        return $this->migrations[$schema]->getLast();
    }

    /**
     * Updates the database by running all migrations that have not yet been
     * performed.
     *
     * @return void
     */
    public function update(): void
    {
        foreach (array_keys($this->migrations) as $schema) {
            while (!$this->updateSchema($schema)) {
            }
        }
    }

    /**
     * Deletes all schemas and their records from the database. Use with
     * extreme caution.
     *
     * @return void
     */
    public function clear(): void
    {
        foreach (array_keys($this->migrations) as $schema) {
            $this->query($schema)->deleteAll();
            $this->migrate($schema)->delete();
            unset($this->migrations[$schema]);
        }
    }

    /**
     * Updates a schema.
     *
     * @param string $name The name of the schema to update.
     * @return boolean True if the schema is up-to-date, false otherwise.
     */
    private function updateSchema(string $name): bool
    {
        // Get the next migration to execute.
        $currentMigration = $this->migrations[$name];
        $migrationArray   = $currentMigration->toArray();
        $nextMigration    = $migrationArray['next'];

        // If the next migration is null, this means that the schema can no
        // longer evolve (for example, because it has been deleted). Therefore,
        // we can delete it.
        if ($nextMigration === null) {
            unset($this->migrations[$name]);
            return true;
        }

        // The next action is not yet defined. This means that the schema is up
        // to date.
        $nextAction = $nextMigration->toArray()['action'];
        if ($nextAction === null) {
            return true;
        }

        // Execute the next migration.
        $this->executeMigration($name, $nextMigration);
        $this->migrations[$name] = $nextMigration;
        return false;
    }
}
