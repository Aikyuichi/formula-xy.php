<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY\Operators;

/**
 * Base class for binary operators that accept two operands.
 */
abstract class BinaryOperator extends Operator {

    /**
     * Applies the operator to two values.
     *
     * @param int|float $value1 First operand.
     * @param int|float $value2 Second operand.
     * @return int|float
     */
    public abstract function resolve($value1, $value2);
}