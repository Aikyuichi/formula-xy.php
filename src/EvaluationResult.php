<?php

use Aikyuichi\FormulaXY\Formula;

/**
 * Stores the result of a formula evaluation attempt.
 */
class EvaluationResult {
    /** @var Formula */
    public $formula;

    /** @var array */
    public $errors;

    /**
     * Creates a new evaluation result.
     *
     * @param Formula $formula Formula instance produced by the evaluation, if valid.
     * @param array $errors List of validation or runtime errors.
     */
    public function __construct(Formula $formula, array $errors) {
        $this->formula = $formula;
        $this->errors = $errors;
    }
}