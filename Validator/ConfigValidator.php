<?php

namespace ScoutUnitsList\Validator;

use ScoutUnitsList\Validator\Condition\StringLengthCondition;

/**
 * Configuration validator
 */
class ConfigValidator extends Validator
{
    /**
     * Set conditions
     */
    protected function setConditions()
    {
        $this->getField('orderNoFormat')
            ->addCondition(new StringLengthCondition(300));
        $this->getField('orderNoPlaceholder')
            ->addCondition(new StringLengthCondition(300));
    }
}
