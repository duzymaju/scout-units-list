<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\FloatHiddenType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Validator\UnitValidator;

/**
 * Unit leader form
 */
class UnitLeaderForm extends BasicForm
{
    /**
     * Set fields
     */
    protected function setFields()
    {
        $this
            ->addField('url', StringType::class, array(
                'label' => __('URL', 'wpcore'),
            ))
            ->addField('mail', StringType::class, array(
                'label' => __('E-mail', 'wpcore'),
            ))
            ->addField('address', StringType::class, array(
                'label' => __('Address', 'wpcore'),
            ))
            ->addField('meetingsTime', StringType::class, array(
                'label' => __('Meetings time', 'wpcore'),
            ))
            ->addField('localizationLat', FloatHiddenType::class, array(
                'label' => __('Localization', 'wpcore'),
            ))
            ->addField('localizationLng', FloatHiddenType::class)
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
        return UnitValidator::class;
    }
}
