<?php

namespace Aikyuichi\FormulaXY\Exceptions;

/**
 * Exception thrown when a formula fails structural validation.
 */
class SyntaxException extends Exception {
    /**
     * Creates an exception for invalid formula syntax.
     *
     * @return SyntaxException
     */
    static public function invalidSyntaxException($message = "Invalid syntax.", $previous = null) {
        return new self($message, self::INVALID_SYNTAX, $previous);
    }

    /**
     * Creates an exception for mismatched parentheses.
     *
     * @return SyntaxException
     */
    static public function mismatchedParenthesesException($message = "Mismatched parentheses.", $previous = null) {
        return new self($message, self::MISMATCHED_PARENTHESES, $previous);
    }
}