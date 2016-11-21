<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator integer condition
 */
class IntegerCondition implements ConditionInterface
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

        if (isset($value) && !is_int($value)) {
            $errors[] = __('This value should be an integer.', 'wpcore');
        }

        return $errors;
    }
}
