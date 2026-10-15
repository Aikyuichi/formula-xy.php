<?php

namespace Aikyuichi\FormulaXY\Tests;

use Aikyuichi\FormulaXY\Exceptions\SyntaxException;
use Aikyuichi\FormulaXY\Operand;
use Aikyuichi\FormulaXY\Operation;
use Aikyuichi\FormulaXY\Operators\Addition;
use PHPUnit\Framework\TestCase;

class OperandsAndExceptionsTest extends TestCase {
    public function testOperandFactoriesPreserveValues() {
        $constant = Operand::constant(5);
        $variable = Operand::variable('{value}', 7);
        $operation = Operand::operation(0, 12);

        $this->assertSame(5, $constant->getValue());
        $this->assertSame(7, $variable->getValue());
        $this->assertSame(12, $operation->getValue());
    }

    public function testOperationResolvesTwoOperands() {
        $operation = new Operation(
            0,
            new Addition(),
            Operand::constant(3),
            Operand::constant(4)
        );

        $this->assertSame(7, $operation->getValue());
    }

    public function testOperationReturnsSingleOperandValue() {
        $operation = new Operation(0, new Addition(), Operand::constant(9));

        $this->assertSame(9, $operation->getValue());
    }

    public function testSyntaxExceptionFactoryMethodsUseExpectedMessagesAndCodes() {
        $syntaxException = SyntaxException::invalidSyntaxException();
        $parenthesesException = SyntaxException::mismatchedParenthesesException();

        $this->assertSame('Invalid syntax.', $syntaxException->getMessage());
        $this->assertSame(SyntaxException::INVALID_SYNTAX, $syntaxException->getCode());
        $this->assertSame('Mismatched parentheses.', $parenthesesException->getMessage());
        $this->assertSame(SyntaxException::MISMATCHED_PARENTHESES, $parenthesesException->getCode());
    }
}
