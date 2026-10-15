<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Aikyuichi\FormulaXY\Exceptions\SyntaxException;
use Aikyuichi\FormulaXY\Formula;

echo "FormulaXY complete example\n";
echo "=========================\n";

try {
    // $formula = new Formula('{a} + {b} * -1');
    // $formula->setVariables([
    //     '{a}' => 5,
    //     '{b}' => 10,
    // ]);
    // print_r($formula->getResult());

    $subtotal = 120.00;
    $taxRate = 8.5;
    $shipping = 15.00;

    $formula = new Formula('({subtotal} * {tax_rate}) / 100 + {shipping}');
    $formula->setVariables([
        '{subtotal}' => $subtotal,
        '{tax_rate}' => $taxRate,
        '{shipping}' => $shipping,
    ]);

    echo 'Expression: ' . $formula->toString() . PHP_EOL;
    echo 'Expanded expression: ' . $formula->toString(true) . PHP_EOL;
    echo 'Components: ' . implode(', ', $formula->getComponents()) . PHP_EOL;
    echo 'Total: ' . $formula->getResult() . PHP_EOL;

    $formula->setVariable('{shipping}', 0.00);
    echo 'Updated total: ' . $formula->getResult() . PHP_EOL;

    $direct = Formula::eval('{x} + {y} * 3', ['allowConstants' => true]);
    if ($direct->formula instanceof Formula) {
        $direct->formula->setVariables([
            '{x}' => 10,
            '{y}' => 4,
        ]);
        echo 'Direct eval result: ' . $direct->formula->getResult() . PHP_EOL;
    } else {
        echo 'Direct eval errors: ' . implode(', ', $direct->errors) . PHP_EOL;
    }

    try {
        new Formula('{x} + )');
    } catch (SyntaxException $e) {
        echo 'Invalid syntax example: ' . $e->getMessage() . PHP_EOL;
    }
} catch (\Throwable $e) {
    echo 'Unexpected error: ' . $e->getMessage() . PHP_EOL;
}