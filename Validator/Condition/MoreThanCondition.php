<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator more than condition
 */
class MoreThanCondition extends NumericCompareCondition
{
    /**
     * Constructor
     *
     * @param int|float $limit limit
     */
    public function __construct($limit)
    {
        parent::__construct($limit, null, true);
    }
}
