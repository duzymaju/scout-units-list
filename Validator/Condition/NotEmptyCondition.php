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
        $errors = [];

        if (empty($value)) {
            $errors[] = __('This value should not be empty.', 'scout-units-list');
        }

        return $errors;
    }
}
