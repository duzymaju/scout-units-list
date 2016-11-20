<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator condition interface
 */
interface ConditionInterface
{
    /**
     * Check
     *
     * @param mixed $value value
     *
     * @return array
     */
    public function check($value);
}
