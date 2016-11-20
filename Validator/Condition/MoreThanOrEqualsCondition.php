<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator more than or equals condition
 */
class MoreThanOrEqualsCondition extends NumericCompareCondition
{
    /**
     * Constructor
     *
     * @param int|float $limit limit
     */
    public function __construct($limit)
    {
        parent::__construct($limit, null, false);
    }
}
