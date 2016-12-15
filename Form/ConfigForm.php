<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\FloatType;
use ScoutUnitsList\Form\Field\IntegerType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Validator\ConfigValidator;

/**
 * Configuration form
 */
class ConfigForm extends Form
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
            ->addField('cacheTtl', IntegerType::class, [
                'label' => __('Cache TTL [seconds]', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('orderNoFormat', StringType::class, [
                'label' => __('Order number format (for "pattern" attribute)', 'scout-units-list'),
            ])
            ->addField('orderNoPlaceholder', StringType::class, [
                'label' => __('Order number placeholder', 'scout-units-list'),
            ])
            ->addField('mapKey', StringType::class, [
                'label' => __('Map key', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('mapDefaultLat', FloatType::class, [
                'label' => __('Map default latitude', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('mapDefaultLng', FloatType::class, [
                'label' => __('Map default longitude', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('mapDefaultZoom', FloatType::class, [
                'label' => __('Map default zoom', 'scout-units-list'),
                'required' => true,
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
        return ConfigValidator::class;
    }
}
