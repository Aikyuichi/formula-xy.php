<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY\Operators;

/**
 * Subtracts the second numeric value from the first.
 */
class Subtraction extends BinaryOperator {

    /**
     * Initializes the subtraction operator.
     */
    public function __construct() {
        parent::__construct('-', 0);
    }

    /**
     * Subtracts the second value from the first.
     *
     * @param int|float $value1 First operand.
     * @param int|float $value2 Second operand.
     * @return int|float
     */
    public function resolve($value1, $value2) {
        return $value1 - $value2;
    }
}