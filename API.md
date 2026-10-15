# FormulaXY API Documentation

This document describes the classes and public APIs defined in the `src/` folder.

## Overview

The package is organized as follows:

- `Formula` — core expression evaluator
- `Operand` — object representing a value in the expression tree
- `Operation` — computed operation with operator and operands
- `Exceptions` — custom validation errors
- `Operators` — arithmetic operator implementations

---

## `src/Formula.php`

Namespace: `Aikyuichi\FormulaXY`

### Class: `Formula`

Evaluates arithmetic expressions with variables, numeric literals, and operators.

### Constants

#### `VARIABLE_EXP`
- Type: `string`
- Value: `'{[^{}]+}'`
- Description: Regular expression used to identify variable tokens such as `{x}`.

#### `CONSTANT_EXP`
- Type: `string`
- Value: `'\d+[.]?\d*'`
- Description: Regular expression used to identify numeric literals such as `10`, `3.5`, or `0.25`.

### Static methods

#### `eval($expression, $options = [])`
- Purpose: Validates an expression and returns a result object without requiring a new instance.
- Parameters:
  - `$expression`: expression string to evaluate
  - `$options`: optional config array
    - `operators`: custom operator list
    - `operands`: allowed operands list
    - `allowConstants`: whether numeric literals are accepted
- Returns: an object containing:
  - `formula`
  - `syntaxValid`
  - `parenthesesValid`
  - `invalidComponents`
  - `errors`

#### `getDefaultOperators()`
- Purpose: Returns the default arithmetic operators.
- Returns: array of `Addition`, `Division`, `Multiplication`, and `Subtraction` instances.

#### `validExpression($expression, $operators)`
- Purpose: Checks whether the expression matches the valid grammar.
- Returns: `true` if valid, otherwise `false`.

#### `validParenthesis($components)`
- Purpose: Verifies that all opened parentheses are properly closed.
- Returns: `true` if balanced, otherwise `false`.

#### `splitExpression($expression, $operators)`
- Purpose: Splits the expression into its component tokens.
- Returns: array of tokens such as variables, operators, and parentheses.

#### `isConstant($component)`
- Purpose: Determines whether a token is a numeric constant.
- Returns: `true` for numeric values.

### Constructor

#### `__construct($expression, $operators = null)`
- Purpose: Creates a new formula evaluator instance.
- Throws: `SyntaxException` if the syntax is invalid or parentheses are malformed.

### Public instance methods

#### `getComponents()`
- Returns: the expression components as an array.

#### `getOperations()`
- Builds the operation stack and returns the resolved operations sequence.
- Used internally to evaluate the expression.

#### `getResult()`
- Returns: final numeric result of the expression.

#### `setVariable($name, $value)`
- Sets a single variable value for the formula.
- Parameters:
  - `$name`: variable placeholder such as `{x}`
  - `$value`: numeric replacement value

#### `setVariables($variables)`
- Sets the variable values for the formula using an associative array.
- Example: `['{x}' => 10, '{y}' => 5]`
- The method updates any matching keys already known to the formula instance.

#### `toString($replaced = false)`
- Returns the original expression string.
- If `$replaced` is `true`, it returns the expression with variables substituted.

### Private methods

#### `toPostFixComponents()`
- Converts the expression into postfix order for evaluation.

#### `isOperator($symbol)`
- Checks whether a token matches one of the configured operators.

#### `getOperator($symbol)`
- Returns the operator instance associated with a symbol.

---

## `src/Operation.php`

Namespace: `Aikyuichi\FormulaXY`

### Class: `Operation`

Represents a single binary operation with its operator and operands.

### Constructor

#### `__construct($id, $operator, $operand1, $operand2 = null)`
- Parameters:
  - `$id`: operation identifier
  - `$operator`: operator instance to apply
  - `$operand1`: first operand
  - `$operand2`: second operand (optional for unary-like use)

### Methods

#### `getValue()`
- Resolves the value of the operation:
  - if `$operand2` is present, it applies `$operator->resolve($operand1, $operand2)`
  - otherwise it returns the value of `$operand1`

---

## `src/Operand.php`

Namespace: `Aikyuichi\FormulaXY`

### Class: `Operand`

Represents a value in the expression tree and stores metadata about whether it is a variable, a constant, or a computed operation.

### Constants

- `TYPE_OPERATION = 1`
- `TYPE_CONSTANT = 2`
- `TYPE_VARIABLE = 3`

### Static factory methods

#### `operation($id, $value)`
- Creates an operand representing a prior computed operation.

#### `constant($value)`
- Creates an operand representing a numeric literal.

#### `variable($name, $value)`
- Creates an operand representing a variable.

### Methods

#### `getValue()`
- Returns the raw value held by the operand.

### Constructor

#### `__construct($name, $type, $value)`
- Private constructor used by the static factory methods.

---

## `src/Exceptions/Exception.php`

Namespace: `Aikyuichi\FormulaXY\Exceptions`

### Class: `Exception`

Base exception class for the package.

### Constants

- `NOT_IMPLEMENTED = 0`
- `INVALID_SYNTAX = 1`
- `MISMATCHED_PARENTHESES = 2`
- `INVALID_VARIABLE = 500`

### Static methods

#### `notImplementedException($message = "Not implemented.", $previous = null)`
- Creates a generic exception for unsupported or unimplemented behavior.

This class extends PHP’s built-in `\Exception`.

---

## `src/Exceptions/FormulaException.php`

Namespace: `Aikyuichi\FormulaXY\Exceptions`

### Class: `FormulaException`

Exception raised when a formula variable or operand is invalid.

### Static methods

#### `invalidVariableException($message = "Invalid variable.", $previous = null)`
- Builds an exception describing an invalid variable or operand condition.

---

## `src/Exceptions/SyntaxException.php`

Namespace: `Aikyuichi\FormulaXY\Exceptions`

### Class: `SyntaxException`

Custom exception for formula validation failures.

### Static methods

#### `invalidSyntaxException($message = "Invalid syntax.", $previous = null)`
- Returns a new `SyntaxException` with code `INVALID_SYNTAX`.

#### `mismatchedParenthesesException($message = "Mismatched parentheses.", $previous = null)`
- Returns a new `SyntaxException` with code `MISMATCHED_PARENTHESES`.

---

## `src/Operators/Operator.php`

Namespace: `Aikyuichi\FormulaXY\Operators`

### Abstract class: `Operator`

Base class for all arithmetic operators.

### Constants

- `PRECEDENCE_IS_LESS = -1`
- `PRECEDENCE_IS_EQUAL = 0`
- `PRECEDENCE_IS_GREATER = 1`

### Properties

- `protected $symbol`
- `protected $precedence`

### Methods

#### `getSymbol()`
- Returns the operator symbol, such as `+`, `-`, `*`, or `/`.

#### `getPrecedence()`
- Returns the precedence level associated with the operator.

#### `comparePrecedence($operator)`
- Compares the precedence of the current operator with another operator.
- Returns one of the precedence constants.

### Constructor

#### `__construct($symbol, $precedence)`
- Protected constructor used by subclasses.

---

## `src/Operators/BinaryOperator.php`

Namespace: `Aikyuichi\FormulaXY\Operators`

### Abstract class: `BinaryOperator`

Base class for binary arithmetic operators.

### Abstract method

#### `resolve($value1, $value2)`
- Implementations must return the result of applying the math operation to two values.

---

## `src/Operators/Addition.php`

Namespace: `Aikyuichi\FormulaXY\Operators`

### Class: `Addition`

Represents addition.

### Constructor

#### `__construct()`
- Initializes the operator with symbol `+` and precedence `0`.

### Method

#### `resolve($value1, $value2)`
- Returns `$value1 + $value2`.

---

## `src/Operators/Subtraction.php`

Namespace: `Aikyuichi\FormulaXY\Operators`

### Class: `Subtraction`

Represents subtraction.

### Constructor

#### `__construct()`
- Initializes the operator with symbol `-` and precedence `0`.

### Method

#### `resolve($value1, $value2)`
- Returns `$value1 - $value2`.

---

## `src/Operators/Multiplication.php`

Namespace: `Aikyuichi\FormulaXY\Operators`

### Class: `Multiplication`

Represents multiplication.

### Constructor

#### `__construct()`
- Initializes the operator with symbol `*` and precedence `1`, above addition and subtraction.

### Method

#### `resolve($value1, $value2)`
- Returns `$value1 * $value2`.

---

## `src/Operators/Division.php`

Namespace: `Aikyuichi\FormulaXY\Operators`

### Class: `Division`

Represents division.

### Constructor

#### `__construct()`
- Initializes the operator with symbol `/` and precedence `1`, above addition and subtraction.

### Method

#### `resolve($value1, $value2)`
- Returns `$value1 / $value2`.

---

## Usage example

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Aikyuichi\FormulaXY\Formula;

$formula = new Formula('{x} + ({y} * 2)');
$formula->setVariables([
    '{x}' => 10,
    '{y}' => 5,
]);

echo $formula->getResult(); // 20
```

---

## Notes

- The package supports a small expression grammar centered around variables, numeric constants, operators, and parentheses.
- Multiplication and division have higher precedence than addition and subtraction. Operators of equal precedence are evaluated left to right.
- Operators are implemented through a simple precedence model based on `Operator::comparePrecedence()`.
- Formula validation is handled through custom syntax and parenthesis checks defined in `Formula` and `SyntaxException`.
