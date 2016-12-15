<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\BooleanType;
use ScoutUnitsList\Form\Field\SelectType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Validator\PositionValidator;

/**
 * Position form
 */
class PositionForm extends Form
{
    /**
     * Set fields
     *
     * @param array $settings settings
     */
    protected function setFields(array $settings)
    {
        unset($settings);

        $this
            ->addField('type', SelectType::class, [
                'label' => __('Type', 'scout-units-list'),
                'options' => UnitAdminForm::getTypes(),
                'required' => true,
            ])
            ->addField('nameMale', StringType::class, [
                'label' => __('Name male', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('nameFemale', StringType::class, [
                'label' => __('Name female', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('description', StringType::class, [
                'label' => __('Description', 'scout-units-list'),
            ])
            ->addField('leader', BooleanType::class, [
                'label' => __('Is unit leader', 'scout-units-list'),
            ])
            ->addField('submit', SubmitType::class, [
                'label' => __('Save', 'scout-units-list'),
            ])
        ;
    }

    /**
     * Get validator class
     *
     * @return string
     */
    protected function getValidatorClass()
    {
        return PositionValidator::class;
    }
}
