<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator string condition
 */
class StringCondition implements ConditionInterface
{
    /**
     * Check
     *
     * @param mixed $value value
     *
     * @return array
     */
    public function check($value)
    {
        $errors = [];

        if (isset($value) && !is_string($value)) {
            $errors[] = __('This value should be a string.', 'scout-units-list');
        }

        return $errors;
    }
}
