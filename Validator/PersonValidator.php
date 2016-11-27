<?php

namespace ScoutUnitsList\Validator;

use ScoutUnitsList\Validator\Condition\MoreThanOrEqualsCondition;
use ScoutUnitsList\Validator\Condition\StringLengthCondition;
use ScoutUnitsList\Validator\Condition\UniqueCondition;

/**
 * Person validator
 */
class PersonValidator extends Validator
{
    /**
     * Set conditions
     *
     * @param array $settings settings
     */
    protected function setConditions(array $settings)
    {
        $this->getForm()
            ->addCondition(new UniqueCondition($settings['repository'], [
                'positionId' => null,
                'unitId' => $this->getForm()
                    ->getModel()
                    ->getUnitId(),
                'userId' => null,
            ]));
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
