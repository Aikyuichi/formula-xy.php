<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY\Operators;

abstract class Operator {
    public const PRECEDENCE_IS_LESS = -1;
    public const PRECEDENCE_IS_EQUAL = 0;
    public const PRECEDENCE_IS_GREATER = 1;

    protected $symbol;
    protected $precedence;

    public function getSymbol() {
        return $this->symbol;
    }

    public function getPrecedence() {
        return $this->precedence;
    }

    public function comparePrecedence($operator) {
        if ($this->precedence < $operator->precedence) {
            return self::PRECEDENCE_IS_LESS;
        } else if ($this->precedence == $operator->precedence) {
            return self::PRECEDENCE_IS_EQUAL;
        } else {
            return self::PRECEDENCE_IS_GREATER;
        }
    }

    protected function __construct($symbol, $precedence) {
        $this->symbol = $symbol;
        $this->precedence = $precedence;
    }
}