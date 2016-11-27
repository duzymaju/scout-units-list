<?php

namespace ScoutUnitsList\Validator;

use ScoutUnitsList\Validator\Condition\StringLengthCondition;

/**
 * Position validator
 */
class PositionValidator extends Validator
{
    /**
     * Set conditions
     *
     * @param array $settings settings
     */
    protected function setConditions(array $settings)
    {
        unset($settings);

        $this->getField('nameMale')
            ->addCondition(new StringLengthCondition(50));
        $this->getField('nameFemale')
            ->addCondition(new StringLengthCondition(50));
        $this->getField('description')
            ->addCondition(new StringLengthCondition(100));
    }
}
