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
class PositionForm extends BasicForm
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
            ->addField('type', SelectType::class, array(
                'label' => __('Type', 'wpcore'),
                'options' => UnitAdminForm::getTypes(),
                'required' => true,
            ))
            ->addField('nameMale', StringType::class, array(
                'label' => __('Name male', 'wpcore'),
                'required' => true,
            ))
            ->addField('nameFemale', StringType::class, array(
                'label' => __('Name female', 'wpcore'),
                'required' => true,
            ))
            ->addField('description', StringType::class, array(
                'label' => __('Description', 'wpcore'),
            ))
            ->addField('leader', BooleanType::class, array(
                'label' => __('Is unit leader', 'wpcore'),
            ))
            ->addField('submit', SubmitType::class, array(
                'label' => __('Save', 'wpcore'),
            ))
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
