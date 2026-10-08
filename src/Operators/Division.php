<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY\Operators;

/**
 * Divides the first numeric value by the second.
 */
class Division extends BinaryOperator {

    /**
     * Initializes the division operator.
     */
    public function __construct() {
        parent::__construct('/', 1);
    }

    /**
     * Divides the first value by the second.
     *
     * @param int|float $value1 First operand (dividend).
     * @param int|float $value2 Second operand (divisor).
     * @return int|float
     */
    public function resolve($value1, $value2) {
        return $value1 / $value2;
    }
}