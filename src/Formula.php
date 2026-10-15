<?php
// Copyright (c) 2025 Aikyuichi <aikyu.sama@gmail.com>
// All rights reserved.
// Use of this source code is governed by a MIT license that can be found in the LICENSE file.

namespace Aikyuichi\FormulaXY;

use Aikyuichi\FormulaXY\Operand;
use Aikyuichi\FormulaXY\Operation;
use Aikyuichi\FormulaXY\Operators\ { Addition, Division, Multiplication, Operator, Subtraction };

/**
 * Evaluates arithmetic formulas built from variables, constants and operators.
 *
 * The engine validates the expression syntax, breaks it into components and
 * resolves it using a postfix conversion and stack-based evaluation.
 */
class Formula {
    /**
     * Regular expression used to identify variables such as {x}.
     */
    public const VARIABLE_EXP = '{[^{}]+}';

    /**
     * Regular expression used to identify numeric constants.
     */
    public const CONSTANT_EXP = '\d+[.]?\d*';

    /** @var string Original formula expression. */
    private $expression;

    /** @var array Available arithmetic operators for this formula. */
    private $operators;

    /** @var array Tokenized formula components. */
    private $components;

    /** @var array<string, int|float> Variable values keyed by their placeholder names. */
    private array $variables = [];

    /**
     * Validates and evaluates an expression without creating a Formula instance.
     *
     * @param string $expression Formula text to analyse.
     * @param array $options Optional evaluation settings.
     * * operators: Allowed operator list
     * * operands: Allowed operand list
     * * allowConstants: Whether to allow numeric literals (default true)
     * @throws Exceptions\SyntaxException When the expression syntax or parentheses are invalid.
     * @return object Result metadata containing the formula instance and validation details.
     */
    static public function eval($expression, $options = []) {
        $operators = !empty($options['operators']) ? $options['operators'] : self::getDefaultOperators();
        $operands = $options['operands'] ?? null;
        $allowConstants = $options['allowConstants'] ?? true;
        
        $syntaxValid = self::validExpression($expression, $operators);
        $components = self::splitExpression($expression, $operators);
        $parenthesisValid = self::validParenthesis($components);
        if (!isset($operands)) {
            $operands = $components;
        }
        $validComponents = array_unique(array_merge(array_map(function($operator) { return $operator->getSymbol(); }, $operators), $operands, ['(', ')']));
        $invalidComponents = array_diff($components, $validComponents);
        if ($allowConstants) {
            $invalidComponents = array_filter($invalidComponents, function($component) { return !self::isConstant($component); });
        }
        $formula = null;
        $error = [];
        if (empty($invalidComponents)) {
            try {
                $formula = new self($expression, $operators);
            } catch(Exceptions\SyntaxException $ex) {
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

    /**
     * Returns the default set of supported arithmetic operators.
     *
     * @return array
     */
    static private function getDefaultOperators() {
        return [
            new Addition(),
            new Division(),
            new Multiplication(),
            new Subtraction(),
        ];
    }

    /**
     * Checks whether the formula syntax is valid for the provided operator set.
     *
     * @param string $expression Formula string to validate.
     * @param array $operators Operators used during validation.
     * @return bool
     */
    static private function validExpression($expression, $operators) {
        $exp = self::VARIABLE_EXP.'|'.self::CONSTANT_EXP;
        $symbols = implode('', array_map(function($operator) { return "\\{$operator->getSymbol()}"; }, $operators));
        $regexp = "/^($exp)\s*([$symbols]\s*($exp)\s*)*$/";
        return preg_match($regexp, str_replace('(', '', str_replace(')', '', trim($expression)))) === 1;
    }

    /**
     * Verifies that every opening parenthesis is matched by a closing one.
     *
     * @param array $components Tokenized formula components.
     * @return bool
     */
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

    /**
     * Splits an expression into its lexical components.
     *
     * @param string $expression Formula string.
     * @param array $operators Available operators.
     * @return array
     */
    static private function splitExpression($expression, $operators) {
        $exp = self::VARIABLE_EXP.'|'.self::CONSTANT_EXP;
        $symbols = implode('', array_map(function($operator) { return "\\{$operator->getSymbol()}"; }, $operators));
        $regexp = "/[$symbols\\(\\)]|$exp/";
        $matches = [];
        preg_match_all($regexp, $expression, $matches);
        return $matches[0];
    }

    /**
     * Determines whether a token is a valid numeric literal.
     *
     * @param string $component Token to inspect.
     * @return bool
     */
    static private function isConstant($component) {
        return filter_var($component, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * Creates a new formula instance after validating the expression.
     *
     * @param string $expression Formula string.
     * @param array|null $operators Custom operator list.
     * @throws Exceptions\SyntaxException When the expression syntax or parentheses are invalid.
     */
    public function __construct($expression, $operators = null) {
        $this->expression = trim($expression);
        $this->operators = $operators ?? self::getDefaultOperators();
        if (!self::validExpression($expression, $this->operators)) {
            throw Exceptions\SyntaxException::invalidSyntaxException();
        }
        $this->components = self::splitExpression($expression, $this->operators);
        if (!self::validParenthesis($this->components)) {
            throw Exceptions\SyntaxException::mismatchedParenthesesException();
        }
    }

    /**
     * Returns the tokenized components of the formula.
     *
     * @return array
     */
    public function getComponents() {
        return $this->components;
    }

    /**
     * Builds and returns the operations required to evaluate the formula.
     *
     * @return array
     */
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
                $operand = Operand::constant($components[0] + 0);
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
                    array_push($stack, Operand::constant($component + 0));
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

    /**
     * Resolves the final value of the expression.
     *
     * @return int|float
     */
    public function getResult() {
        $operations = $this->getOperations();
        return end($operations)->getValue();
    }

    /**
     * Sets the value for a single variable in the expression.
     *
     * @param string $name Variable placeholder, such as {x}.
     * @param int|float $value Numeric replacement value.
     * @return void
     */
    public function setVariable($name, $value) {
        $this->variables[$name] = $value;
    }

    /**
     * Sets the replacement values for every variable in the expression.
     *
     * @param array $variables Map of variable names to numeric values.
     */
    public function setVariables($variables) {
        foreach ($variables as $name => $value) {
            $this->setVariable($name, $value);
        }
    }

    /**
     * Returns the original expression or a version with the configured variables applied.
     *
     * @param bool $replaced Whether to replace placeholders in the expression.
     * @return string
     */
    public function toString($replaced = false) {
        if ($replaced) {
            return strtr($this->expression, $this->variables);
        }
        return $this->expression;
    }

    /**
     * Converts the formula into postfix notation so it can be evaluated with a stack.
     *
     * @return array
     */
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

    /**
     * Checks whether a token matches a configured operator symbol.
     *
     * @param string $symbol Token to inspect.
     * @return bool
     */
    private function isOperator($symbol) {
        foreach ($this->operators as $operator) {
            if ($operator->getSymbol() == $symbol) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the operator instance matching a symbol.
     *
     * @param string $symbol Operator symbol.
     * @return Operator|null
     */
    private function getOperator($symbol) {
        foreach ($this->operators as $operator) {
            if ($operator->getSymbol() == $symbol) {
                return $operator;
            }
        }
        return null;
    }
}