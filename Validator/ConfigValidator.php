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
     *
     * @param array $settings settings
     */
    protected function setConditions(array $settings)
    {
        unset($settings);

        $this->getField('orderNoFormat')
            ->addCondition(new StringLengthCondition(300));
        $this->getField('orderNoPlaceholder')
            ->addCondition(new StringLengthCondition(300));
    }
}
