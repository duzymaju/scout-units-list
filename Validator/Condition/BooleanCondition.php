<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator boolean condition
 */
class BooleanCondition implements ConditionInterface
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
        $errors = array();

        if (isset($value) && !is_bool($value)) {
            $errors[] = __('This value should be a boolean.', 'wpcore');
        }

        return $errors;
    }
}
