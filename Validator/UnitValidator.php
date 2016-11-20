<?php

namespace ScoutUnitsList\Validator;

use ScoutUnitsList\Validator\Condition\MoreThanOrEqualsCondition;
use ScoutUnitsList\Validator\Condition\StringLengthCondition;

/**
 * Unit validator
 */
class UnitValidator extends BasicValidator
{
    /**
     * Set conditions
     */
    protected function setConditions()
    {
        $this->getField('sort')
            ->addCondition(new MoreThanOrEqualsCondition(0));
        $this->getField('name')
            ->addCondition(new StringLengthCondition(50));
        $this->getField('nameFull')
            ->addCondition(new StringLengthCondition(100));
        $this->getField('hero')
            ->addCondition(new StringLengthCondition(50));
        $this->getField('heroFull')
            ->addCondition(new StringLengthCondition(100));
        $this->getField('url')
            ->addCondition(new StringLengthCondition(100));
        $this->getField('mail')
            ->addCondition(new StringLengthCondition(100));
        $this->getField('address')
            ->addCondition(new StringLengthCondition(100));
        $this->getField('meetingsTime')
            ->addCondition(new StringLengthCondition(50));
    }
}
