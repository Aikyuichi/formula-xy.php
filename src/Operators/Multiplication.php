<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY\Operators;

/**
 * Multiplies two numeric values.
 */
class Multiplication extends BinaryOperator {

    /**
     * Initializes the multiplication operator.
     */
    public function __construct() {
        parent::__construct('*', 1);
    }

    /**
     * Multiplies the two provided values.
     *
     * @param int|float $value1 First operand.
     * @param int|float $value2 Second operand.
     * @return int|float
     */
    public function resolve($value1, $value2) {
        return $value1 * $value2;
    }
}