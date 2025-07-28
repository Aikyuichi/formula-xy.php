<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY\Operators;

class DivisionOperator extends BinaryOperator {

    public function __construct() {
        parent::__construct('/', 0);
    }

    public function resolve($value1, $value2) {
        return $value1 / $value2;
    }
}