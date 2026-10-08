<?php

namespace Aikyuichi\FormulaXY\Tests;

use Aikyuichi\FormulaXY\Exceptions\SyntaxException;
use Aikyuichi\FormulaXY\Formula;
use PHPUnit\Framework\TestCase;

class FormulaTest extends TestCase {
    public function testEvaluatesArithmeticUsingOperatorPrecedence() {
        $formula = new Formula('2 + 3 * 4');

        $this->assertSame(14, $formula->getResult());
    }

    public function testParenthesesOverrideOperatorPrecedence() {
        $formula = new Formula('(2 + 3) * 4');

        $this->assertSame(20, $formula->getResult());
    }

    public function testEvaluatesVariablesAndFormatsExpression() {
        $formula = new Formula('{price} * {quantity} - 2');
        $formula->setVariables([
            '{price}' => 3,
            '{quantity}' => 4,
        ]);

        $this->assertSame(10, $formula->getResult());
        $this->assertSame('{price} * {quantity} - 2', $formula->toString());
        $this->assertSame('3 * 4 - 2', $formula->toString(true));
    }

    public function testReturnsExpressionComponents() {
        $formula = new Formula('({x}+2) * 3');

        $this->assertSame(['(', '{x}', '+', '2', ')', '*', '3'], $formula->getComponents());
    }

    public function testEvaluatesSingleConstantAndVariable() {
        $constant = new Formula('42');
        $variable = new Formula('{answer}');
        $variable->setVariables(['{answer}' => 42]);

        $this->assertSame(42, $constant->getResult());
        $this->assertSame(42, $variable->getResult());
    }

    public function testEvalReturnsAFormulaForValidExpression() {
        $result = Formula::eval('2 + 3 * 4');

        $this->assertTrue($result->syntaxValid);
        $this->assertTrue($result->parenthesesValid);
        $this->assertInstanceOf(Formula::class, $result->formula);
        $this->assertSame(14, $result->formula->getResult());
    }

    public function testConstructorThrowsForInvalidSyntax() {
        try {
            new Formula('2 + * 3');
            $this->fail('Expected invalid syntax to throw an exception.');
        } catch (SyntaxException $exception) {
            $this->assertSame(SyntaxException::INVALID_SYNTAX, $exception->getCode());
        }
    }

    public function testConstructorThrowsForUnbalancedParentheses() {
        try {
            new Formula('(2 + 3');
            $this->fail('Expected invalid parentheses to throw an exception.');
        } catch (SyntaxException $exception) {
            $this->assertSame(SyntaxException::INVALID_PARENTHESES, $exception->getCode());
        }
    }
}
