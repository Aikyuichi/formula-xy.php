<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY;

class Operator {
    public const PRECEDENCE_IS_LESS = -1;
    public const PRECEDENCE_IS_EQUAL = 0;
    public const PRECEDENCE_IS_GREATER = 1;

    private $symbol;
    private $precedence;
    private $resolveCallback;

    static public function getOperators() {
        return [
            self::addition(),
            self::subtraction(),
            self::multiplication(),
            self::division(),
        ];
    }
    
    static public function addition() {
        return new Operator('+', 0, function($v1, $v2) {
            return $v1 + $v2;
        });
    }

    static public function subtraction() {
        return new Operator('-', 0, function($v1, $v2) {
            return $v1 - $v2;
        });
    }

    static public function multiplication() {
        return new Operator('*', 1, function($v1, $v2) {
            return $v1 * $v2;
        });
    }

    static public function division() {
        return new Operator('/', 1, function($v1, $v2) {
            return $v1 / $v2;
        });
    }

    public function getSymbol() {
        return $this->symbol;
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

    public function resolve($value1, $value2) {
        return $this->resolveCallback->call($this, $value1, $value2);
    }

    private function __construct($symbol, $precedence, $resolveCallback) {
        $this->symbol = $symbol;
        $this->precedence = $precedence;
        $this->resolveCallback = $resolveCallback;
    }
}