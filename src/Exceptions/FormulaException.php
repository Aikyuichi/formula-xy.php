<?php

namespace Aikyuichi\FormulaXY\Exceptions;

/**
 * Exception raised when a formula variable or operand is invalid.
 */
class FormulaException extends Exception {
    /**
     * Creates an exception for an invalid notation.
     *
     * @param string $message Descriptive error message.
     * @param \Throwable|null $previous Previous exception for chaining.
     * @return FormulaException
     */
    static public function invalidNotationException($message = "Invalid notation.", $previous = null) {
        return new self($message, self::INVALID_NOTATION, $previous);
    }

    /**
     * Creates an exception for an invalid variable.
     *
     * @param string $message Descriptive error message.
     * @param \Throwable|null $previous Previous exception for chaining.
     * @return FormulaException
     */
    static public function invalidVariableException($message = "Invalid variable.", $previous = null) {
        return new self($message, self::INVALID_VARIABLE, $previous);
    }
}