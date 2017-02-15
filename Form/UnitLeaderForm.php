<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\EmailType;
use ScoutUnitsList\Form\Field\FloatHiddenType;
use ScoutUnitsList\Form\Field\IntegerType;
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
        if ($settings['canManageUnits']) {
            $this->addField('sort', IntegerType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Sort', 'scout-units-list'),
            ]);
        }
        $this
            ->addField('url', UrlType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('URL', 'scout-units-list'),
                'nullable' => true,
            ])
            ->addField('mail', EmailType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('E-mail', 'scout-units-list'),
                'nullable' => true,
            ])
            ->addField('address', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Address', 'scout-units-list'),
                'nullable' => true,
            ])
            ->addField('meetingsTime', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Meetings time', 'scout-units-list'),
                'nullable' => true,
            ])
            ->addField('localizationLat', FloatHiddenType::class, [
                'label' => __('Localization', 'scout-units-list'),
                'nullable' => true,
            ])
            ->addField('localizationLng', FloatHiddenType::class, [
                'nullable' => true,
            ])
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
