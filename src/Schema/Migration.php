<?php

declare(strict_types=1);

namespace Nucleus\Schema;

use Exception;
use Nucleus\Schema\Exceptions\MigrationErrorException;

/**
 * A schema migration is an object used to build schemas. It represents a small,
 * reversible step in the schema construction process.
 */
class Migration
{
    /**
     * Schema creation action.
     */
    const ACTION_CREATE = 'schema:create';

    /**
     * Schema deletion action.
     */
    const ACTION_DELETE = 'schema:delete';

    /**
     * Field addition action.
     */
    const ACTION_FIELD_ADD = 'field:add';

    /**
     * Field removal action.
     */
    const ACTION_FIELD_REMOVE = 'field:remove';

    /**
     * Field modification action.
     */
    const ACTION_FIELD_MODIFY = 'field:modify';

    /**
     * Indicates the action performed by this migration.
     *
     * @var string
     */
    private $action;

    /**
     * The migration parameters.
     *
     * @var array
     */
    private $parameters;

    /**
     * The array used to build the schema.
     *
     * @var array
     */
    private $schemaArr;

    /**
     * The next migration.
     *
     * @var Migration
     */
    private $next;

    /**
     * Initializes the schema migration.
     */
    public function __construct()
    {
        $this->action     = null;
        $this->parameters = null;
        $this->schemaArr  = null;
        $this->next       = null;
    }

    /**
     * Creates the schema.
     *
     * @param array $schemaArr The schema array representation.
     * @return Migration The next migration.
     */
    public function create(array $schemaArr = []): Migration
    {
        try {
            $schema = new Schema($schemaArr);
            $this->schemaArr  = $schema->toArray();

            $this->setAction(self::ACTION_CREATE);
            return $this->next();
        } catch (Exception $e) {
            $msg = 'Error while creating schema: ' . $e->getMessage();
            throw new MigrationErrorException($msg);
        }
    }

    /**
     * Deletes the schema.
     *
     * @return void
     */
    public function delete(): void
    {
        if ($this->schemaArr === null) {
            $msg = "A schema must be associated to this migration.";
            throw new MigrationErrorException($msg);
        }

        $this->schemaArr = null;
    }

    /**
     * Adds a field to the schema.
     *
     * @param string $name The field name.
     * @param array $fieldArr The field array representation.
     * @return Migration The next migration.
     */
    public function addField(string $name, array $fieldArr): Migration
    {
        if ($this->schemaArr === null) {
            $msg = "A schema must be associated to this migration.";
            throw new MigrationErrorException($msg);
        }

        if (empty($name)) {
            $msg = "Field names cannot be empty.";
            throw new MigrationErrorException($msg);
        }

        if (array_key_exists($name, $this->schemaArr)) {
            $msg = "Duplicate schema field $name.";
            throw new MigrationErrorException($msg);
        }

        try {
            $field = new Field($name, $fieldArr);
            $this->schemaArr[$name] = $field->toArray();
            $this->parameters = ['name' => $name, 'array' => $fieldArr];

            $this->setAction(self::ACTION_FIELD_ADD);
            return $this->next();
        } catch (Exception $e) {
            $msg = 'Error while creating schema: ' . $e->getMessage();
            throw new MigrationErrorException($msg);
        }
    }

    /**
     * Removes a field of the schema.
     *
     * @param string $name The name of the field to remove.
     * @return Migration The next migration.
     */
    public function removeField(string $name): Migration
    {
        if ($this->schemaArr === null) {
            $msg = "A schema must be associated to this migration.";
            throw new MigrationErrorException($msg);
        }

        if (!array_key_exists($name, $this->schemaArr)) {
            $msg = "Field $name cannot be deleted because it does not exist.";
            throw new MigrationErrorException($msg);
        }

        unset($this->schemaArr[$name]);
        $this->parameters = ['name' => $name];

        $this->setAction(self::ACTION_FIELD_REMOVE);
        return $this->next();
    }

    /**
     * Modifies the definition of an existing field.
     *
     * @param string $name The name of the field to modify.
     * @param array $newValues The values that must be updated.
     * @return Migration The next migration.
     */
    public function modifyField(string $name, array $newValues): Migration
    {
        if ($this->schemaArr === null) {
            $msg = "A schema must be associated to this migration.";
            throw new MigrationErrorException($msg);
        }

        if (!array_key_exists($name, $this->schemaArr)) {
            $msg = "Field $name cannot be modified because it does not exist.";
            throw new MigrationErrorException($msg);
        }

        try {
            $oldFieldArr = $this->schemaArr[$name];
            $newFieldArr = array_merge($oldFieldArr, $newValues);

            $field = new Field($name, $newFieldArr);
            $this->schemaArr[$name] = $field->toArray();
            $this->parameters = ['name' => $name, 'values' => $newValues];

            $this->setAction(self::ACTION_FIELD_MODIFY);
            return $this->next();
        } catch (Exception $e) {
            $msg = 'Error while creating schema: ' . $e->getMessage();
            throw new MigrationErrorException($msg);
        }
    }

    /**
     * Returns the schema built by this migration and the previous ones.
     *
     * @return Schema A schema object.
     */
    public function schema(): Schema
    {
        return new Schema($this->schemaArr);
    }

    /**
     * Returns the array representation of the migration.
     *
     * @return array The array representation.
     */
    public function toArray(): array
    {
        // Get the next migration.
        $next = null;
        if ($this->next !== null && $this->next->action !== null) {
            $next = $this->next->toArray();
        }

        // Build the array.
        return [
            'action'     => $this->action,
            'parameters' => $this->parameters,
            'schema'     => $this->schemaArr,
            'next'       => $next
        ];
    }

    /**
     * Returns the next migration.
     *
     * @return Migration
     */
    protected function next(): Migration
    {
        if ($this->action === null) {
            $msg = 'Cannot get the next migration of an empty migration';
            throw new MigrationErrorException($msg);
        }

        if ($this->next !== null) {
            $msg = 'The next migration is already set.';
            throw new MigrationErrorException($msg);
        }

        $migration = new Migration();
        $migration->schemaArr = $this->schemaArr;
        $migration->next = null;

        $this->next = $migration;
        return $this->next;
    }

    /**
     * Sets the action performed by this migration.
     *
     * @param string $action
     * @return void
     */
    protected function setAction(string $action): void
    {
        if ($this->action !== null) {
            $msg = 'The action is already set.';
            throw new MigrationErrorException($msg);
        }

        $this->action = $action;
    }
}
