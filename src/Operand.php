<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY;

class Operand {
    public const TYPE_OPERATION = 1;
    public const TYPE_CONSTANT = 2;
    public const TYPE_VARIABLE = 3;

    private $name;
    private $type;
    private $value;

    static public function operation($id, $value) {
        return new self(
            "operation:$id",
            self::TYPE_OPERATION,
            $value
        );
    }

    static public function constant($value) {
        return new self(
            "constant:$value",
            self::TYPE_CONSTANT,
            $value
        );
    }

    static public function variable($name, $value) {
        return new self(
            $name,
            self::TYPE_VARIABLE,
            $value
        );
    }

    public function getValue() {
        return $this->value;
    }

    private function __construct($name, $type, $value) {
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
    }
}