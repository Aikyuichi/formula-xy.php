<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY;

class Operation {
    private $id;
    private $operator;
    private $operand1;
    private $operand2;

    public function __construct($id, $operator, $operand1, $operand2 = null) {
        $this->id = $id;
        $this->operator = $operator;
        $this->operand1 = $operand1;
        $this->operand2 = $operand2;
    }

    public function getValue() {
        if (isset($operand2)) {
            return $this->operator->resolve($this->operand1->getValue(), $this->operand2->getValue());
        }
        return $this->operand1->getValue();
    }
}