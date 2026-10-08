# FormulaXY

![Static Badge](https://img.shields.io/badge/PHP-≥7.2-orange)
![Packagist Version](https://img.shields.io/packagist/v/Aikyuichi/formula-xy)
![Packagist License](https://img.shields.io/packagist/l/Aikyuichi/formula-xy)

A lightweight PHP library for evaluating mathematical expressions with variables, constants, and arithmetic operators.

## Features

- Supports variables using `{name}` syntax
- Accepts numeric constants such as `12`, `3.5`, and `0.25`
- Default operators: `+`, `-`, `*`, `/`
- Supports parenthesized expressions like `({x} + {y}) * 2`
- Handles invalid syntax and mismatched parentheses
- Provides a simple API to evaluate expressions and inspect their components

## Installation

```bash
composer require aikyuichi/formula-xy
```

Then, in your project:

```php
require __DIR__ . '/vendor/autoload.php';
```

## Basic usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Aikyuichi\FormulaXY\Formula;

try {
    $formula = new Formula('{x} + {y} * 5');
    $formula->setVariables([
        '{x}' => 10,
        '{y}' => 3,
    ]);

    echo $formula->getResult(); // 25
} catch (\Throwable $e) {
    echo $e->getMessage();
}
```

## Supported syntax

Expressions may include:

- Variables: `{x}`, `{total}`, `{price_with_tax}`
- Constants: `10`, `3.14`, `0.5`
- Operators: `+`, `-`, `*`, `/`
- Parentheses: `(` and `)`

Valid examples:

```php
'{x} + {y}'
'{x} * ({y} - 2)'
'({price} / 100) * {quantity}'
'10 + {tax}'
```

Invalid examples:

```php
'{x} ** {y}'
'{x} + )'
'((1 + 2'
```

## Direct evaluation

You can also evaluate an expression without manually instantiating the object:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Aikyuichi\FormulaXY\Formula;

$result = Formula::eval('{x} - {y}', [
    'allowConstants' => true,
]);

var_dump($result);
```

The `Formula::eval()` method returns an object with the following information:

- `formula`: a `Formula` instance if the expression is valid
- `syntaxValid`: indicates whether the syntax is valid
- `parenthesesValid`: validates the parenthesis balance
- `invalidComponents`: unrecognized components
- `errors`: detected errors

## Available methods

### `new Formula(string $expression, ?array $operators = null)`

Creates a formula from the given expression. If no custom operators are provided, the default operators are used: `+`, `-`, `*`, `/`.

### `setVariables(array $variables): void`

Assigns values to variables. The keys must follow the `{name}` format.

```php
$formula->setVariables([
    '{x}' => 8,
    '{y}' => 2,
]);
```

### `getResult(): mixed`

Returns the numeric result of the expression.

```php
echo $formula->getResult();
```

### `getComponents(): array`

Returns the tokenized components of the expression.

### `getOperations(): array`

Returns the sequence of operations used to compute the result.

### `toString(bool $replaced = false): string`

Returns the original expression, or a version with variables replaced if `true` is passed.

## Usage examples

### Discount calculation

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Aikyuichi\FormulaXY\Formula;

$formula = new Formula('({price} * {percent}) / 100');
$formula->setVariables([
    '{price}' => 500,
    '{percent}' => 15,
]);

echo $formula->getResult(); // 75
```

### Parenthesized operations

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Aikyuichi\FormulaXY\Formula;

$formula = new Formula('({a} + {b}) * ({c} - 2)');
$formula->setVariables([
    '{a}' => 4,
    '{b}' => 6,
    '{c}' => 10,
]);

echo $formula->getResult(); // 80
```

## Error handling

The library throws exceptions when the expression is invalid:

```php
<?php

use Aikyuichi\FormulaXY\Formula;
use Aikyuichi\FormulaXY\Exceptions\SyntaxException;

try {
    $formula = new Formula('{x} + )');
} catch (SyntaxException $e) {
    echo $e->getMessage();
}
```

## API documentation

For the complete reference of all classes and methods in the `src/` folder, see [API.md](API.md).

## Running tests

Install the development dependencies and run the PHPUnit test suite:

```bash
composer install
composer test
```

## License

FormulaXY is available under the MIT license. See the [LICENSE](LICENSE) file for more details.

## Author

Aikyuichi <aikyu.sama@gmail.com>
