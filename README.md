# FormulaXY
![Static Badge](https://img.shields.io/badge/PHP-≥7.2-orange)
![Packagist Version](https://img.shields.io/packagist/v/Aikyuichi/formula-xy)
![Packagist License](https://img.shields.io/packagist/l/Aikyuichi/formula-xy)

Formula evaluator

## Usage
```php
require_once(__DIR__ . '/vendor/autoload.php');

try {
    $formula = new Aikyuichi\FormulaXY\Formula('{x}-{y}*5');
    $formula->setVariables([
        '{x}' => 10,
        '{y}' => 5,
    ]);
    print($formula->getResult());
} catch (\Exception $ex) {
    print($ex);
}
```

## Author
Aikyuichi, aikyu.sama@gmail.com

## License
FormulaXY is available under the MIT license. See the LICENSE file for more info.