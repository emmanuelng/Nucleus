<?php

declare(strict_types=1);

namespace Nucleus\Database\Databases;

use Nucleus\Database\Database;
use Nucleus\Database\Exceptions\DatabaseInternalException;
use Nucleus\Database\Exceptions\DatabaseQueryException;
use Nucleus\Database\Query;
use Nucleus\Database\Record;
use Nucleus\Database\Selector;
use Nucleus\Schema\Migration;

/**
 * A database implementation that stores its records in memory.
 */
class MockDatabase extends Database
{
    /**
     * The records for each schema.
     *
     * @var array
     */
    private $records;

    /**
     * Initializes the database.
     */
    public function __construct()
    {
        $this->records = [];
    }

    /**
     * {@inheritDoc}
     */
    public function executeQuery(Query $query)
    {
        $queryArray = $query->toArray();
        $schemaName = $queryArray['schema'];
        $values     = $queryArray['values'];
        $where      = $queryArray['where'];

        if (!array_key_exists($schemaName, $this->records)) {
            $msg = "Schema $schemaName is not supported or does not exist.";
            throw new DatabaseQueryException($msg);
        }

        $schema  = $this->getSchema($schemaName);
        $records = &$this->records[$schemaName];

        switch ($queryArray['action']) {
            case Query::ACTION_CREATE:
                $records[] = $values;
                return new Record($schema, $values);

            case Query::ACTION_UPDATE:
                foreach ($records as $index => $record) {
                    if ($this->isSelected($where, $record)) {
                        $updated = array_merge($record, $values);
                        $records[$index] = $updated;
                    }
                }
                return;

            case Query::ACTION_GET:
                $result = [];
                foreach ($records as $index => $record) {
                    if ($this->isSelected($where, $record)) {
                        $result[] = new Record($schema, $record);
                    }
                }
                return $result;

            case Query::ACTION_DELETE:
                for ($i = 0; $i < count($records); $i++) {
                    if ($this->isSelected($where, $records[$i])) {
                        array_splice($records, $i, 1);
                    }
                }
                return;

            default:
                $msg = 'Unsupported query action ' . $queryArray['action'];
                throw new DatabaseInternalException($msg);
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function executeMigration(string $schema, Migration $migration): void
    {
        $array = $migration->toArray();

        switch ($array['action']) {
            case Migration::ACTION_CREATE:
                $this->records[$schema] = [];
                break;

            case Migration::ACTION_DELETE:
                unset($this->records[$schema]);
                break;

            case Migration::ACTION_FIELD_ADD:
                $field = $array['parameters']['name'];
                foreach ($this->records[$schema] as &$record) {
                    $record[$field] = null;
                }
                break;

            case Migration::ACTION_FIELD_MODIFY:
                $field  = $array['parameters']['name'];
                $values = $array['parameters']['values'];

                foreach ($this->records[$schema] as &$record) {
                    if (isset($values['list'])) {
                        if ($values['list']) {
                            $record[$field] = [$record[$field]];
                        } else {
                            $record[$field] = $record[$field][0] ?? null;
                        }
                    }
                }
                break;

            case Migration::ACTION_FIELD_REMOVE:
                $field = $array['parameters']['name'];
                foreach ($this->records[$schema] as &$record) {
                    unset($record[$field]);
                }
                break;

            default:
                $msg = 'Unsupported migration action ' . $array['action'];
                throw new DatabaseInternalException($msg);
                break;
        }
    }

    /**
     * Returns whether a record is selected.
     *
     * @param array|null $where  The 'where' array.
     * @param array      $record The record to test.
     *
     * @return boolean True if the record is selected, false otherwise.
     */
    private function isSelected(?array $where, array $record): bool
    {
        // If the where clause is empty, all records are selected.
        if ($where == null) {
            return true;
        }

        $selected = null;

        foreach ($where as $condition) {
            // Evaluate the first condition
            if ($selected === null) {
                $selected = $this->evaluateCondition($condition, $record);
                continue;
            }

            // Evaluate the following conditions
            switch ($condition['operand']) {
                case Selector::OPERAND_AND:
                    $selected &= $this->evaluateCondition($condition, $record);
                    break;

                case Selector::OPERAND_OR:
                    $selected |= $this->evaluateCondition($condition, $record);
                    break;

                default:
                    break;
            }
        }

        return $selected === null ? false : $selected == 1;
    }

    /**
     * Evaluates a condition on a record.
     *
     * @param array $condition The condition.
     * @param array $record    The record.
     *
     * @return boolean The condition result.
     */
    private function evaluateCondition(array $condition, array $record): bool
    {
        // If there is a sub-condition, evaluate it.
        if ($condition['subCondition'] != null) {
            $arr = $condition['subCondition'];
            return $this->evaluateCondition($arr, $record);
        }

        // Otherwise, evaluate the condition itself.
        switch ($condition['operator']) {
            case Selector::OPERATOR_EQ:
                $key = $condition['left'];
                return $record[$key] == $condition['right'];

            case Selector::OPERATOR_NEQ:
                return $record['left'] != $condition['right'];

            case Selector::OPERATOR_GT:
                $key = $condition['left'];
                return $record[$key] > $condition['right'];

            case Selector::OPERATOR_GTE:
                $key = $condition['left'];
                return $record[$key] >= $condition['right'];

            case Selector::OPERATOR_LT:
                $key = $condition['left'];
                return $record[$key] < $condition['right'];

            case Selector::OPERATOR_LTE:
                $key = $condition['left'];
                return $record[$key] <= $condition['right'];

            case Selector::OPERATOR_LIKE:
                $key   = $condition['left'];
                $regex = '/^' . $condition['right'] . '$/';
                $regex = preg_replace('/%/', '.*', $regex);
                $regex = preg_replace('/_/', '.', $regex);
                return preg_match($regex, $record[$key]);

            default:
                break;
        }

        return true;
    }
}
