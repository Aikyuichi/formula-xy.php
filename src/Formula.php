<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY;

use Aikyuichi\FormulaXY\Operand;
use Aikyuichi\FormulaXY\Operation;
use Aikyuichi\FormulaXY\Operators\ { AdditionOperator, DivisionOperator, MultiplicationOperator, Operator, SubtractionOperator };
use Aikyuichi\FormulaXY\Exceptions\SyntaxException;

class Formula {
    public const VARIABLE_EXP = '{[^{}]+}';
    public const CONSTANT_EXP = '\d+[.]?\d*';

    private $expression;
    private $operators;
    private $components;
    private $variables = [];

    static public function eval($expression, $options = []) {
        $operators = $options['operators'] ?? self::getDefaultOperators();
        $operands = $options['operands'] ?? null;
        $allowConstants = $options['allowConstants'] ?? true;
        
        $syntaxValid = self::validExpression($expression, $operators);
        $components = self::splitExpression($expression, $operators);
        $parenthesisValid = self::validParenthesis($components);
        if (!isset($operands)) {
            $operands = $components;
        }
        $invalidComponents = array_diff($components, $operands);
        if ($allowConstants) {
            $invalidComponents = array_filter($invalidComponents, function($component) { return !self::isConstant($component); });
        }
        $formula = null;
        $error = [];
        if (empty($invalidComponents)) {
            try {
                $formula = new self($expression, $operators);
            } catch(SyntaxException $ex) {
                $errors[$ex->getCode()] = $ex->getMessage();
            }
        }
        return (object)[
            'formula' => $formula,
            'syntaxValid' => $syntaxValid,
            'parenthesesValid' => $parenthesisValid,
            'invalidComponents' => $invalidComponents,
            'errors' => $error,
        ];
    }

    static private function getDefaultOperators() {
        return [
            new AdditionOperator(),
            new DivisionOperator(),
            new MultiplicationOperator(),
            new SubtractionOperator(),
        ];
    }

    static private function validExpression($expression, $operators) {
        $exp = self::VARIABLE_EXP.'|'.self::CONSTANT_EXP;
        $symbols = implode('', array_map(function($operator) { return "\\{$operator->getSymbol()}"; }, $operators));
        $regexp = "/^($exp)\s*([$symbols]\s*($exp)\s*)*$/";
        return preg_match($regexp, str_replace('(', '', str_replace(')', '', trim($expression)))) === 1;
    }

    static private function validParenthesis($components) {
        $parenthesis = [];
        foreach ($components as $component) {
            if ($component == '(') {
                array_push($parenthesis, $component);
            } else if ($component == ')') {
                if (count($parenthesis) > 0) {
                    array_pop($parenthesis);
                } else {
                    return false;
                }
            }
        }
        return count($parenthesis) == 0;
    }

    static private function splitExpression($expression, $operators) {
        $exp = self::VARIABLE_EXP.'|'.self::CONSTANT_EXP;
        $symbols = implode('', array_map(function($operator) { return "\\{$operator->getSymbol()}"; }, $operators));
        $regexp = "/[$symbols\\(\\)]|$exp/";
        $matches = [];
        preg_match_all($regexp, $expression, $matches);
        return $matches[0];
    }

    static private function isConstant($component) {
        return filter_var($component, FILTER_VALIDATE_FLOAT) !== false;
    }

    public function __construct($expression, $operators = null) {
        $this->expression = trim($expression);
        $this->operators = $operators ?? self::getDefaultOperators();
        if (!self::validExpression($expression, $operators)) {
            throw SyntaxException::invalidSyntax();
        }
        $this->components = self::splitExpression($expression, $operators);
        if (!self::validParenthesis($components)) {
            throw SyntaxException::invalidParentheses();
        }
    }

    public function getComponents() {
        return $this->components;
    }

    public function getOperations() {
        $variableExp = self::VARIABLE_EXP;
        $components = $this->toPostFixComponents();
        $operations = [];
        $stack = [];
        if (count($components) == 1) {
            $operand = null;
            if (preg_match("/^$variableExp$/", $components[0])) {
                $operand = Operand::variable($components[0], $this->variables[$components[0]] ?? null);
            } else if (self::isConstant($components[0])) {
                $operand = Operand::constant($components[0]);
            }
            if (isset($operand)) {
                $operations[] = new Operation(0, $this->getOperator('+'), $operand);
            }
        } else {
            $index = 0;
            foreach ($components as $component) {
                if (preg_match("/^$variableExp$/", $component)) {
                    array_push($stack, Operand::variable($component, $this->variables[$component] ?? null));
                } else if (self::isConstant($component)) {
                    array_push($stack, Operand::constant($component));
                } else if ($this->isOperator($component)) {
                    $operand2 = array_pop($stack);
                    $operand1 = array_pop($stack);
                    $operations[] = new Operation($index, $this->getOperator($component), $operand1, $operand2);
                    array_push($stack, Operand::operation($index, $operations[$index]->getValue()));
                    $index++;
                }
            }
        }
        return $operations;
    }

    public function getResult() {
        $operations = $this->getOperations();
        return end($operations)->getValue();
    }

    public function setVariables($variables) {
        $this->variables = $variables;
    }

    public function toString($replaced = false) {
        if ($replaced) {
            return strtr($this->expression, $this->variables);
        }
        return $this->expression;
    }

    private function toPostFixComponents() {
        $variableExp = self::VARIABLE_EXP;
        $stack = [];
        $components = [];
        $stackItem = [];
        foreach ($this->components as $component) {
            if (preg_match("/^$variableExp$/", $component)) {
                $components[] =$component;
            } else if (self::isConstant($component)) {
                $components[] = $component;
            } else if ($component == '(') {
                array_push($stack, $component);
            } else if ($component == ')') {
                while (!empty($stack)) {
                    if (end($stack) == '(') {
                        array_pop($stack);
                        break;
                    } else {
                        $components[] = array_pop($stack);
                    }
                }
            } else if ($this->isOperator($component)) {
                while (!empty($stack)) {
                    $stackItem = end($stack);
                    if (!$this->isOperator($stackItem)) {
                        break;
                    }
                    $operator1 = $this->getOperator($component);
                    $operator2 = $this->getOperator($stackItem);
                    if ($operator1->comparePrecedence($operator2) <= Operator::PRECEDENCE_IS_EQUAL) {
                        $components[] = array_pop($stack);
                    } else {
                        break;
                    }
                }
                array_push($stack, $component);
            }
        }
        while (!empty($stack)) {
            $components[] = array_pop($stack);
        }
        return $components;
    }

    private function isOperator($symbol) {
        foreach ($this->operators as $operator) {
            if ($operator->getSymbol() == $symbol) {
                return true;
            }
        }
        return false;
    }

    private function getOperator($symbol) {
        foreach ($this->operators as $operator) {
            if ($operator->getSymbol() == $symbol) {
                return $operator;
            }
        }
        return null;
    }
}