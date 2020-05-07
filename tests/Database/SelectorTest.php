<?php

declare(strict_types=1);

namespace Tests\Database;

use Nucleus\Database\Exceptions\DatabaseQueryException;
use Nucleus\Database\Selector;
use PHPUnit\Framework\TestCase;
use Tests\Database\Classes\DatabaseTestFactory;

/**
 * Tests the Selector class.
 */
class SelectorTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        $database = DatabaseTestFactory::database();
        $database->clear();
        $database->addSchema('test', []);
    }

    /**
     * Tests whether the 'equals' operator and its variants work correctly.
     *
     * @return void
     */
    public function testOperatorEquals(): void
    {
        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_EQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->eq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_EQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andEq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_EQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orEq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_EQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andNotEq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_EQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orNotEq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_EQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->notEq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andEq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andNotEq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orEq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orNotEq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->eq('left', 'right');
            $selector->eq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->eq('left', 'right');
            $selector->notEq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }
    }

    /**
     * Tests whether the 'not equals' operator and its variants work correctly.
     *
     * @return void
     */
    public function testOperatorNotEquals(): void
    {
        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_NEQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->neq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_NEQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andNeq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_NEQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orNeq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_NEQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andNotNeq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_NEQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orNotNeq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_NEQ,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->notNeq('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andNeq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andNotNeq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orNeq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orNotNeq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->neq('left', 'right');
            $selector->neq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->neq('left', 'right');
            $selector->notNeq('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }
    }

    /**
     * Tests whether the 'greater than' operator and its variants work correctly.
     *
     * @return void
     */
    public function testOperatorGreaterThan(): void
    {
        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->gt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andGt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orGt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andNotGt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orNotGt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->notGt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andGt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andNotGt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orGt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orNotGt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->gt('left', 'right');
            $selector->gt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->gt('left', 'right');
            $selector->notGt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }
    }

    /**
     * Tests whether the 'greater than or equals' operator and its variants work correctly.
     *
     * @return void
     */
    public function testOperatorGreaterThanOrEquals(): void
    {
        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->gte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andGte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orGte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andNotGte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orNotGte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_GTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->notGte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andGte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andNotGte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orGte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orNotGte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->gte('left', 'right');
            $selector->gte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->gte('left', 'right');
            $selector->notGte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }
    }

    /**
     * Tests whether the 'less than' operator and its variants work correctly.
     *
     * @return void
     */
    public function testOperatorLessThan(): void
    {
        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->lt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andLt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orLt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andNotLt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orNotLt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LT,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->notLt('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andLt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andNotLt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orLt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orNotLt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->lt('left', 'right');
            $selector->lt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->lt('left', 'right');
            $selector->notLt('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }
    }

    /**
     * Tests whether the 'less than or equals' operator and its variants work correctly.
     *
     * @return void
     */
    public function testOperatorLessThanOrEquals(): void
    {
        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->lte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andLte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => false,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orLte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_AND,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->andNotLte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orNotLte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'  => null,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LTE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->notLte('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andLte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andNotLte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orLte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orNotLte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->lte('left', 'right');
            $selector->lte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->lte('left', 'right');
            $selector->notLte('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }
    }

    /**
     * Tests whether the 'like' operator and its variants work correctly.
     *
     * @return void
     */
    public function testOperatorLike(): void
    {
        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'      => null,
            'not'          => false,
            'left'         => 'left',
            'operator'     => Selector::OPERATOR_LIKE,
            'right'        => 'right',
            'subCondition' => null
        ];
        $this->assertSame($selector, $selector->like('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'      => Selector::OPERAND_AND,
            'not'          => false,
            'left'         => 'left',
            'operator'     => Selector::OPERATOR_LIKE,
            'right'        => 'right',
            'subCondition' => null
        ];
        $this->assertSame($selector, $selector->andLike('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'      => Selector::OPERAND_OR,
            'not'          => false,
            'left'         => 'left',
            'operator'     => Selector::OPERATOR_LIKE,
            'right'        => 'right',
            'subCondition' => null
        ];
        $this->assertSame($selector, $selector->orLike('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'      => Selector::OPERAND_AND,
            'not'          => true,
            'left'         => 'left',
            'operator'     => Selector::OPERATOR_LIKE,
            'right'        => 'right',
            'subCondition' => null
        ];
        $this->assertSame($selector, $selector->andNotLike('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $refArray[] = [
            'operand'  => Selector::OPERAND_OR,
            'not'      => true,
            'left'     => 'left',
            'operator' => Selector::OPERATOR_LIKE,
            'right'    => 'right',
            'subCondition'    => null
        ];
        $this->assertSame($selector, $selector->orNotLike('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        $selector = DatabaseTestFactory::selector('test');
        $refArray = [];

        $refArray[] = [
            'operand'      => null,
            'not'          => true,
            'left'         => 'left',
            'operator'     => Selector::OPERATOR_LIKE,
            'right'        => 'right',
            'subCondition' => null
        ];
        $this->assertSame($selector, $selector->notLike('left', 'right'));
        $this->assertSame($refArray, $selector->toArray());

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andLike('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->andNotLike('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orLike('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->orNotLike('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->like('left', 'right');
            $selector->like('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }

        try {
            $selector = DatabaseTestFactory::selector('test');
            $selector->like('left', 'right');
            $selector->notLike('left', 'right');
            $this->fail('Selector inconsistencies allowed.');
        } catch (DatabaseQueryException $e) {
            // There must be an exception.
        }
    }
}
