<?php

namespace Aikyuichi\FormulaXY\Exceptions;

/**
 * Base exception for formula validation errors.
 */
class Exception extends \Exception {
    /** @var int */
    public const NOT_IMPLEMENTED = 0;

    /** @var int */
    public const INVALID_SYNTAX = 1;

    /** @var int */
    public const MISMATCHED_PARENTHESES = 2;

    /** @var int */
    public const INVALID_VARIABLE = 500;

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