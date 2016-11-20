<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator less than or equals condition
 */
class LessThanOrEqualsCondition extends NumericCompareCondition
{
    /**
     * Constructor
     *
     * @param int|float $limit limit
     */
    public function __construct($limit)
    {
        parent::__construct(null, $limit, false);
    }
}
