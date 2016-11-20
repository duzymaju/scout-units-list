<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator not empty condition
 */
class NotEmptyCondition implements ConditionInterface
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

        if (empty($value)) {
            $errors[] = __('This value should not be empty.', 'wpcore');
        }

        return $errors;
    }
}
