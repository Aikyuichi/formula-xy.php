<?php

namespace Aikyuichi\FormulaXY\Tests;

use Aikyuichi\FormulaXY\Operators\Addition;
use Aikyuichi\FormulaXY\Operators\Division;
use Aikyuichi\FormulaXY\Operators\Multiplication;
use Aikyuichi\FormulaXY\Operators\Operator;
use Aikyuichi\FormulaXY\Operators\Subtraction;
use PHPUnit\Framework\TestCase;

class OperatorsTest extends TestCase {
    public function testAdditionOperator() {
        $operator = new Addition();

        $this->assertSame('+', $operator->getSymbol());
        $this->assertSame(0, $operator->getPrecedence());
        $this->assertSame(7, $operator->resolve(3, 4));
    }

    public function testSubtractionOperator() {
        $operator = new Subtraction();

        $this->assertSame('-', $operator->getSymbol());
        $this->assertSame(0, $operator->getPrecedence());
        $this->assertSame(2, $operator->resolve(5, 3));
    }

    public function testMultiplicationOperator() {
        $operator = new Multiplication();

        $this->assertSame('*', $operator->getSymbol());
        $this->assertSame(1, $operator->getPrecedence());
        $this->assertSame(12, $operator->resolve(3, 4));
    }

    public function testDivisionOperator() {
        $operator = new Division();

        $this->assertSame('/', $operator->getSymbol());
        $this->assertSame(1, $operator->getPrecedence());
        $this->assertSame(2, $operator->resolve(8, 4));
    }

    public function testOperatorPrecedenceComparison() {
        $addition = new Addition();
        $multiplication = new Multiplication();

        $this->assertSame(Operator::PRECEDENCE_IS_LESS, $addition->comparePrecedence($multiplication));
        $this->assertSame(Operator::PRECEDENCE_IS_GREATER, $multiplication->comparePrecedence($addition));
        $this->assertSame(Operator::PRECEDENCE_IS_EQUAL, $addition->comparePrecedence(new Subtraction()));
    }
}
