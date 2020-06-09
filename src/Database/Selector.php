<?php

declare(strict_types=1);

namespace Nucleus\Database;

use Nucleus\Database\Exceptions\DatabaseInternalException;
use Nucleus\Database\Exceptions\DatabaseQueryException;

/**
 * Class used to select records.
 */
class Selector
{
    /**
     * The "AND" operand.
     */
    const OPERAND_AND = 'AND';

    /**
     * The "OR" operand.
     */
    const OPERAND_OR = 'OR';

    /**
     * Equals operator.
     */
    const OPERATOR_EQ = '=';

    /**
     * Not equals operator.
     */
    const OPERATOR_NEQ = '!=';

    /**
     * Less than operator.
     */
    const OPERATOR_LT = '<';

    /**
     * More than operator.
     */
    const OPERATOR_GT = '>';

    /**
     * Less than or equal operator.
     */
    const OPERATOR_LTE = '<=';

    /**
     * Greater than or equal operator.
     */
    const OPERATOR_GTE = '>=';

    /**
     * Like operator
     */
    const OPERATOR_LIKE = 'LIKE';

    /**
     * The associated query.
     *
     * @var Query
     */
    private $query;

    /**
     * The list of conditions.
     *
     * @var array
     */
    private $conditions;

    /**
     * Initializes the selector.
     *
     * @param Query $query The associated query.
     */
    public function __construct(?Query $query)
    {
        $this->query      = $query;
        $this->conditions = [];
    }

    /**
     * Adds a sub-condition.
     *
     * @param callable $builder A callable that takes a Selector as parameter.
     *
     * @return Selector This selector.
     */
    public function where(callable $builder): Selector
    {
        $where = new Selector(null);
        call_user_func($builder, $where);

        $this->addSubCondition(
            null,
            false,
            $where
        );

        return $this;
    }

    /**
     * Adds a negated sub-condition.
     *
     * @param callable $builder A callable that takes a Selector as parameter.
     *
     * @return Selector This selector.
     */
    public function notWhere(callable $builder): Selector
    {
        $where = new Selector(null);
        call_user_func($builder, $where);

        $this->addSubCondition(
            null,
            true,
            $where
        );

        return $this;
    }

    /**
     * Adds a sub-condition with the 'and' operand.
     *
     * @param callable $builder A callable that takes a Selector as parameter.
     *
     * @return Selector This selector.
     */
    public function andWhere(callable $builder): Selector
    {
        $where = new Selector(null);
        call_user_func($builder, $where);

        $this->addSubCondition(
            self::OPERAND_AND,
            false,
            $where
        );

        return $this;
    }

    /**
     * Adds a sub-condition with the 'and not' operand.
     *
     * @param callable $builder A callable that takes a Selector as parameter.
     *
     * @return Selector This selector.
     */
    public function andNotWhere(callable $builder): Selector
    {
        $where = new Selector(null);
        call_user_func($builder, $where);

        $this->addSubCondition(
            self::OPERAND_AND,
            true,
            $where
        );

        return $this;
    }

    /**
     * Adds a sub-condition with the 'or' operand.
     *
     * @param callable $builder A callable that takes a Selector as parameter.
     *
     * @return Selector This selector.
     */
    public function orWhere(callable $builder): Selector
    {
        $where = new Selector(null);
        call_user_func($builder, $where);

        $this->addSubCondition(
            self::OPERAND_OR,
            false,
            $where
        );

        return $this;
    }

    /**
     * Adds a sub-condition with the 'or not' operand.
     *
     * @param callable $builder A callable that takes a Selector as parameter.
     *
     * @return Selector This selector.
     */
    public function orNotWhere(callable $builder): Selector
    {
        $where = new Selector(null);
        call_user_func($builder, $where);

        $this->addSubCondition(
            self::OPERAND_OR,
            true,
            $where
        );

        return $this;
    }

    /**
     * Adds an "equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function eq(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            false,
            $left,
            self::OPERATOR_EQ,
            $right
        );

        return $this;
    }

    /**
     * Adds a "not equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function notEq(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            true,
            $left,
            self::OPERATOR_EQ,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andEq(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            false,
            $left,
            self::OPERATOR_EQ,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and not equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andNotEq(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            true,
            $left,
            self::OPERATOR_EQ,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orEq(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            false,
            $left,
            self::OPERATOR_EQ,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or not equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orNotEq(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            true,
            $left,
            self::OPERATOR_EQ,
            $right
        );

        return $this;
    }

    /**
     * Adds a "not equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function neq(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            false,
            $left,
            self::OPERATOR_NEQ,
            $right
        );

        return $this;
    }

    /**
     * Adds a "not not equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function notNeq(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            true,
            $left,
            self::OPERATOR_NEQ,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and not equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andNeq(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            false,
            $left,
            self::OPERATOR_NEQ,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and not not equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andNotNeq(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            true,
            $left,
            self::OPERATOR_NEQ,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or not equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orNeq(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            false,
            $left,
            self::OPERATOR_NEQ,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or not not equals" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orNotNeq(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            true,
            $left,
            self::OPERATOR_NEQ,
            $right
        );

        return $this;
    }

    /**
     * Adds a "greater than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function gt(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            false,
            $left,
            self::OPERATOR_GT,
            $right
        );

        return $this;
    }

    /**
     * Adds a "not greater than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function notGt(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            true,
            $left,
            self::OPERATOR_GT,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and greater than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andGt(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            false,
            $left,
            self::OPERATOR_GT,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and not greater than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andNotGt(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            true,
            $left,
            self::OPERATOR_GT,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or greater than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orGt(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            false,
            $left,
            self::OPERATOR_GT,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or not greater than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orNotGt(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            true,
            $left,
            self::OPERATOR_GT,
            $right
        );

        return $this;
    }

    /**
     * Adds a "less than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function lt(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            false,
            $left,
            self::OPERATOR_LT,
            $right
        );

        return $this;
    }

    /**
     * Adds a "not less than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function notLt(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            true,
            $left,
            self::OPERATOR_LT,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and less than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andLt(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            false,
            $left,
            self::OPERATOR_LT,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and not less than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andNotLt(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            true,
            $left,
            self::OPERATOR_LT,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or less than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orLt(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            false,
            $left,
            self::OPERATOR_LT,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or not less than" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orNotLt(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            true,
            $left,
            self::OPERATOR_LT,
            $right
        );

        return $this;
    }

    /**
     * Adds a "greaterthan or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function gte(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            false,
            $left,
            self::OPERATOR_GTE,
            $right
        );

        return $this;
    }

    /**
     * Adds a "not greater than or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function notGte(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            true,
            $left,
            self::OPERATOR_GTE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and greater than or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andGte(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            false,
            $left,
            self::OPERATOR_GTE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and not greater than or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andNotGte(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            true,
            $left,
            self::OPERATOR_GTE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or greater than or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orGte(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            false,
            $left,
            self::OPERATOR_GTE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or not greater than or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orNotGte(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            true,
            $left,
            self::OPERATOR_GTE,
            $right
        );

        return $this;
    }

    /**
     * Adds a "less than or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function lte(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            false,
            $left,
            self::OPERATOR_LTE,
            $right
        );

        return $this;
    }

    /**
     * Adds a "not less than or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function notLte(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            true,
            $left,
            self::OPERATOR_LTE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and less than or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andLte(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            false,
            $left,
            self::OPERATOR_LTE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and not less than or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andNotLte(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            true,
            $left,
            self::OPERATOR_LTE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or less than or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orLte(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            false,
            $left,
            self::OPERATOR_LTE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or not less than or equal" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orNotLte(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            true,
            $left,
            self::OPERATOR_LTE,
            $right
        );

        return $this;
    }

    /**
     * Adds a "like" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function like(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            false,
            $left,
            self::OPERATOR_LIKE,
            $right
        );

        return $this;
    }

    /**
     * Adds a "not like" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function notLike(string $left, $right): Selector
    {
        $this->addCondition(
            null,
            true,
            $left,
            self::OPERATOR_LIKE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and like" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andLike(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            false,
            $left,
            self::OPERATOR_LIKE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "and not like" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function andNotLike(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_AND,
            true,
            $left,
            self::OPERATOR_LIKE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or like" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orLike(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            false,
            $left,
            self::OPERATOR_LIKE,
            $right
        );

        return $this;
    }

    /**
     * Adds an "or not like" condition.
     *
     * @param string $left  The left-hand side.
     * @param mixed  $right The right-hand side.
     *
     * @return Selector This selector.
     */
    public function orNotLike(string $left, $right): Selector
    {
        $this->addCondition(
            self::OPERAND_OR,
            true,
            $left,
            self::OPERATOR_LIKE,
            $right
        );

        return $this;
    }

    /**
     * Returns the records in the selection.
     *
     * @return array An array of records.
     */
    public function get(): array
    {
        if ($this->query === null) {
            $msg = 'Cannot execute query in its context.';
            throw new DatabaseInternalException($msg);
        }

        return $this->query->getAll();
    }

    /**
     * Updates all the selected records with the given values.
     *
     * @param array $values The values.
     * @return void
     */
    public function update(array $values): void
    {
        if ($this->query === null) {
            $msg = 'Cannot execute query in its context.';
            throw new DatabaseInternalException($msg);
        }

        $this->query->updateAll($values);
    }

    /**
     * Deletes the selected records.
     *
     * @return void
     */
    public function delete(): void
    {
        if ($this->query === null) {
            $msg = 'Cannot execute query in its context.';
            throw new DatabaseInternalException($msg);
        }

        $this->query->deleteAll();
    }

    /**
     * Returns the array representation of this selector.
     *
     * @return array The array representation.
     */
    public function toArray(): array
    {
        return $this->conditions;
    }

    /**
     * Adds a condition to the selector.
     *
     * @param string|null $operand  The operand.
     * @param boolean     $not      True if the condition is negated.
     * @param string      $left     The left-hand-side.
     * @param string|null $operator The operator.
     * @param mixed       $right    The right-hand-side.
     *
     * @return void
     */
    private function addCondition(
        ?string $operand,
        bool $not,
        string $left,
        ?string $operator,
        $right
    ) {
        if ($operand === null && !empty($this->conditions)) {
            $msg = 'There must be an operand.';
            throw new DatabaseQueryException($msg);
        }

        if ($operand !== null && empty($this->conditions)) {
            $msg = 'There must be at least one condition.';
            throw new DatabaseQueryException($msg);
        }

        $this->conditions[] = [
            'operand'      => $operand,
            'not'          => $not,
            'left'         => $left,
            'operator'     => $operator,
            'right'        => $right,
            'subCondition' => null
        ];
    }

    /**
     * Adds a sub-condition.
     *
     * @param string|null $operand   The operand.
     * @param boolean     $not       True if the condition is negated.
     * @param Selector    $condition The sub-condition.
     *
     * @return void
     */
    private function addSubCondition(
        ?string $operand,
        bool $not,
        Selector $condition
    ) {
        if ($operand === null && !empty($this->conditions)) {
            $msg = 'There must be an operand.';
            throw new DatabaseQueryException($msg);
        }

        if (empty($this->conditions)) {
            $msg = 'There must be at least one condition.';
            throw new DatabaseQueryException($msg);
        }

        $this->conditions[] = [
            'operand'      => $operand,
            'not'          => $not,
            'left'         => null,
            'operator'     => null,
            'right'        => null,
            'subCondition' => $condition->toArray()
        ];
    }
}
