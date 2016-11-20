<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\EmailType;
use ScoutUnitsList\Form\Field\FloatHiddenType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Form\Field\UrlType;
use ScoutUnitsList\Validator\UnitValidator;

/**
 * Unit leader form
 */
class UnitLeaderForm extends BasicForm
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
            ->addField('url', UrlType::class, array(
                'label' => __('URL', 'wpcore'),
            ))
            ->addField('mail', EmailType::class, array(
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
