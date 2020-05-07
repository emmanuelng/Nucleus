<?php

declare(strict_types=1);

namespace Nucleus\Database;

use Exception;
use Nucleus\Database\Exceptions\DatabaseInternalException;
use Nucleus\Database\Exceptions\DatabaseQueryException;
use Nucleus\Schema\Schema;

/**
 * Represents database query.
 */
class Query
{
    /**
     * Get action.
     */
    const ACTION_GET = 'get';

    /**
     * Create action.
     */
    const ACTION_CREATE = 'create';

    /**
     * Update action.
     */
    const ACTION_UPDATE = 'update';

    /**
     * Delete action.
     */
    const ACTION_DELETE = 'delete';

    /**
     * The database on which the query will be executed.
     *
     * @var Database
     */
    private $database;

    /**
     * The name of the schema associated to this query.
     *
     * @var string
     */
    private $schemaName;

    /**
     * The schema associated to this query.
     *
     * @var Schema
     */
    private $schema;

    /**
     * The action that must be performed.
     *
     * @var string
     */
    private $action;

    /**
     * The values of the query.
     *
     * @var array
     */
    private $values;

    /**
     * The record selection.
     *
     * @var Selector
     */
    private $where;

    /**
     * Initializes the query.
     *
     * @param Database $database The database.
     * @param string $schemaName The name of the schema associated to this
     * query.
     */
    public function __construct(Database $database, string $schemaName)
    {
        // Initialize the attributes.
        $this->database   = $database;
        $this->schemaName = $schemaName;
        $this->schema     = $database->getSchema($schemaName);
        $this->action     = null;
        $this->values     = null;
        $this->where      = null;

        // Check if the schema was found.
        if ($this->schema === null) {
            $msg = "The schema $schemaName is not supported by the database "
                . "or does not exist.";
            throw new DatabaseQueryException($msg);
        }
    }

    /**
     * Initializes and returns the query selection.
     *
     * @return Selector A selector.
     */
    public function where(): Selector
    {
        if ($this->where === null) {
            $this->where = new Selector($this);
        }

        return $this->where;
    }

    /**
     * Gets records.
     *
     * @return array An array of recors.
     */
    public function getAll(): array
    {
        $this->action = self::ACTION_GET;
        $result = $this->database->executeQuery($this);

        if (!is_array($result)) {
            $msg = 'A Get query must return an array of records.';
            throw new DatabaseInternalException($msg);
        }

        $records = [];

        foreach ($result as $record) {
            if ($record instanceof Record) {
                $records[] = $record;
                continue;
            }

            $msg = 'The items of a Get query must be instances of '
                . Record::class;
            throw new DatabaseInternalException($msg);
        }

        return $records;
    }

    /**
     * Adds a new record.
     *
     * @param array $values The record's value.
     * @return Record A record.
     */
    public function create(array $values): Record
    {
        try {
            $this->action = self::ACTION_CREATE;
            $this->values = $this->schema->filter($values);

            $record = $this->database->executeQuery($this);
            if ($record instanceof Record) {
                return $record;
            }

            $msg = 'A Create query must return the created record.';
            throw new DatabaseInternalException($msg);
        } catch (Exception $e) {
            $msg = 'Error : ' . $e->getMessage();
            throw new DatabaseQueryException($msg);
        }
    }

    /**
     * Updates records.
     *
     * @param array $values The values to update.
     * @return void
     */
    public function updateAll(array $values): void
    {
        foreach ($values as $field => $value) {
            $fieldObj = $this->schema->field($field);
            if ($fieldObj !== null) {
                $this->values[$field] = $fieldObj->filter($value);
            }
        }

        $this->action = self::ACTION_UPDATE;
        $this->database->executeQuery($this);
    }

    /**
     * Deletes records.
     *
     * @return void
     */
    public function deleteAll(): void
    {
        $this->action = self::ACTION_DELETE;
        $this->database->executeQuery($this);
    }

    /**
     * Returns an array representation of this query.
     *
     * @return array The array representaion.
     */
    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'schema' => $this->schemaName,
            'where'  => $this->where == null ? null : $this->where->toArray(),
            'values' => $this->values
        ];
    }
}
