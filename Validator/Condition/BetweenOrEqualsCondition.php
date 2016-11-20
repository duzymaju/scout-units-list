<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator between or equals condition
 */
class BetweenOrEqualsCondition extends NumericCompareCondition
{
    /**
     * Constructor
     *
     * @param int|float $lowerLimit lower limit
     * @param int|float $upperLimit upper limit
     */
    public function __construct($lowerLimit, $upperLimit)
    {
        parent::__construct($lowerLimit, $upperLimit, false);
    }
}
