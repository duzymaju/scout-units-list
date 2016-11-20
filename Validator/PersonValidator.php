<?php

namespace ScoutUnitsList\Validator;

use ScoutUnitsList\Validator\Condition\MoreThanOrEqualsCondition;
use ScoutUnitsList\Validator\Condition\StringLengthCondition;

/**
 * Person validator
 */
class PersonValidator extends BasicValidator
{
    /**
     * Set conditions
     */
    protected function setConditions()
    {
        $this->getField('userId')
            ->addCondition(new MoreThanOrEqualsCondition(1));
        $this->getField('unitId')
            ->addCondition(new MoreThanOrEqualsCondition(1));
        $this->getField('positionId')
            ->addCondition(new MoreThanOrEqualsCondition(1));
        $this->getField('orderNo')
            ->addCondition(new StringLengthCondition(50));
    }
}
