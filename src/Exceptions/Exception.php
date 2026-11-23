<?php

namespace Aikyuichi\FormulaXY\Exceptions;

/**
 * Base exception for formula validation errors.
 */
class Exception extends \Exception {
    /** @var int */
    const NOT_IMPLEMENTED = 0;

    /** @var int */
    const INVALID_SYNTAX = 1;

    /** @var int */
    const MISMATCHED_PARENTHESES = 2;

    /** @var int */
    const INVALID_NOTATION = 500;

    /** @var int */
    const INVALID_VARIABLE = 501;

    /**
     * Creates a generic not-implemented exception.
     *
     * @param string $message Human-readable error message.
     * @param \Throwable|null $previous Previous exception for chaining.
     * @return Exception
     */
    static public function notImplementedException($message = "Not implemented.", $previous = null) {
        return new self($message, self::NOT_IMPLEMENTED, $previous);
    }
}