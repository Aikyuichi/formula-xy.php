<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY;

use Aikyuichi\FormulaXY\Operators\Operator;
use Aikyuichi\FormulaXY\Operators\BinaryOperator;

/**
 * Represents a single mathematical operation in the evaluation process.
 */
class Operation {
    /** @var mixed */
    private $id;

    /** @var Operator */
    private $operator;

    /** @var Operand */
    private $operand1;

    /** @var Operand */
    private $operand2;

    /**
     * Creates a new operation instance.
     *
     * @param mixed $id Operation identifier.
     * @param Operator $operator Operator implementation to execute.
     * @param Operand $operand1 First operand.
     * @param Operand $operand2 Second operand, if any.
     */
    public function __construct($id, $operator, $operand1, $operand2 = null) {
        $this->id = $id;
        $this->operator = $operator;
        $this->operand1 = $operand1;
        $this->operand2 = $operand2;
    }

    /**
     * Resolves and returns the operation result.
     *
     * @return int|float
     */
    public function getValue() {
        if (!isset($this->operand2)) {
            return $this->operand1->getValue();   
        }
        if ($this->operator instanceof BinaryOperator) {
            return $this->operator->resolve($this->operand1->getValue(), $this->operand2->getValue());
        }
        throw Exceptions\Exception::notImplementedException("{gettype($this->operator)} is not implemented.");
    }
}