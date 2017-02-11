<?php

namespace ScoutUnitsList\Validator;

use ScoutUnitsList\Validator\Condition\StringLengthCondition;

/**
 * Versioned delete validator
 */
class VersionedDeleteValidator extends Validator
{
    /**
     * Set conditions
     *
     * @param array $settings settings
     */
    protected function setConditions(array $settings)
    {
        unset($settings);

        $this->getField('orderNo')
            ->addCondition(new StringLengthCondition(50));
    }
}
