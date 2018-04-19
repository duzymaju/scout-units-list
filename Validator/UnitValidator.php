<?php

namespace ScoutUnitsList\Validator;

use ScoutUnitsList\Validator\Condition\MoreThanOrEqualsCondition;
use ScoutUnitsList\Validator\Condition\StringLengthCondition;
use ScoutUnitsList\Validator\Condition\TypesDependencyCondition;

/**
 * Unit validator
 */
class UnitValidator extends Validator
{
    /**
     * Set conditions
     *
     * @param array $settings settings
     */
    protected function setConditions(array $settings)
    {
        if (array_key_exists('unitRepository', $settings) && array_key_exists('typesManager', $settings)) {
            $this->getForm()
                ->addCondition(new TypesDependencyCondition($settings['unitRepository'], $settings['typesManager'],
                    'type', 'subtype', 'parentId'));
        }
        $this->getField('sort')
            ->addCondition(new MoreThanOrEqualsCondition(0));
        $this->getField('orderNo')
            ->addCondition(new StringLengthCondition(50));
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
        $this->getField('markerUrl')
            ->addCondition(new StringLengthCondition(100));
    }
}
