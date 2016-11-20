<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator less than condition
 */
class LessThanCondition extends NumericCompareCondition
{
    /**
     * Constructor
     *
     * @param int|float $limit limit
     */
    public function __construct($limit)
    {
        parent::__construct(null, $limit, true);
    }
}
