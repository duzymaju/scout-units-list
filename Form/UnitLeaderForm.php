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
class UnitLeaderForm extends Form
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
            ->addField('url', UrlType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('URL', 'scout-units-list'),
            ])
            ->addField('mail', EmailType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('E-mail', 'scout-units-list'),
            ])
            ->addField('address', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Address', 'scout-units-list'),
            ])
            ->addField('meetingsTime', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Meetings time', 'scout-units-list'),
            ])
            ->addField('localizationLat', FloatHiddenType::class, [
                'label' => __('Localization', 'scout-units-list'),
            ])
            ->addField('localizationLng', FloatHiddenType::class)
            ->addField('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'button button-primary',
                ],
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
        return UnitValidator::class;
    }
}
