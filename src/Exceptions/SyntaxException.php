<?php

namespace Aikyuichi\FormulaXY\Exceptions;

class SyntaxException extends Exception {
    
    static public function invalidSyntax() {
        return new self('Invalid syntax.', self::INVALID_SYNTAX);
    }

    static public function invalidParentheses() {
        return new self('Invalid parentheses.', self::INVALID_PARENTHESES);
    }
}