<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY\Operators;

/**
 * Base implementation for all arithmetic operators.
 */
abstract class Operator {
    /** @var int */
    public const PRECEDENCE_IS_LESS = -1;

    /** @var int */
    public const PRECEDENCE_IS_EQUAL = 0;

    /** @var int */
    public const PRECEDENCE_IS_GREATER = 1;

    /** @var string */
    protected $symbol;

    /** @var int */
    protected $precedence;

    /**
     * Returns the operator symbol.
     *
     * @return string
     */
    public function getSymbol() {
        return $this->symbol;
    }

    /**
     * Returns the operator precedence level.
     *
     * @return int
     */
    public function getPrecedence() {
        return $this->precedence;
    }

    /**
     * Compares the precedence of this operator against another operator.
     *
     * @param Operator $operator Operator to compare with.
     * @return int One of the precedence constants.
     */
    public function comparePrecedence($operator) {
        if ($this->precedence < $operator->precedence) {
            return self::PRECEDENCE_IS_LESS;
        } else if ($this->precedence == $operator->precedence) {
            return self::PRECEDENCE_IS_EQUAL;
        } else {
            return self::PRECEDENCE_IS_GREATER;
        }
    }

    /**
     * Initializes an operator with a symbol and precedence.
     *
     * @param string $symbol Operator token.
     * @param int $precedence Precedence level.
     */
    protected function __construct($symbol, $precedence) {
        $this->symbol = $symbol;
        $this->precedence = $precedence;
    }
}