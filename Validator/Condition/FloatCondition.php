<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator float condition
 */
class FloatCondition implements ConditionInterface
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

        if (isset($value) && !is_float($value)) {
            $errors[] = __('This value should be a float.', 'wpcore');
        }

        return $errors;
    }
}
