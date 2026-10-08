<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY;

/**
 * Represents a value in the formula expression tree.
 */
class Operand {
    /** @var int */
    public const TYPE_OPERATION = 1;

    /** @var int */
    public const TYPE_CONSTANT = 2;

    /** @var int */
    public const TYPE_VARIABLE = 3;

    /** @var string */
    private $name;

    /** @var int */
    private $type;

    /** @var int|float */
    private $value;

    /**
     * Creates an operand representing a computed operation.
     *
     * @param mixed $id Operation identifier.
     * @param int|float $value Computed value.
     * @return Operand
     */
    static public function operation($id, $value) {
        return new self(
            "operation:$id",
            self::TYPE_OPERATION,
            $value
        );
    }

    /**
     * Creates an operand representing a constant numeric literal.
     *
     * @param int|float $value Constant value.
     * @return Operand
     */
    static public function constant($value) {
        return new self(
            "constant:$value",
            self::TYPE_CONSTANT,
            $value
        );
    }

    /**
     * Creates an operand representing a variable.
     *
     * @param string $name Variable name.
     * @param int|float $value Variable value.
     * @return Operand
     */
    static public function variable($name, $value) {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("Variable value must be numeric.");
        }
        return new self(
            $name,
            self::TYPE_VARIABLE,
            $value
        );
    }

    /**
     * Returns the operand value.
     *
     * @return int|float
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Initializes the operand with its identifier, type and value.
     *
     * @param string $name Operand name.
     * @param int $type Operand type.
     * @param int|float $value Operand value.
     */
    private function __construct($name, $type, $value) {
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
    }
}