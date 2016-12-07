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
                'label' => __('Cache TTL [seconds]', 'wpcore'),
                'required' => true,
            ])
            ->addField('orderNoFormat', StringType::class, [
                'label' => __('Order number format (for "pattern" attribute)', 'wpcore'),
            ])
            ->addField('orderNoPlaceholder', StringType::class, [
                'label' => __('Order number placeholder', 'wpcore'),
            ])
            ->addField('mapKey', StringType::class, [
                'label' => __('Map key', 'wpcore'),
                'required' => true,
            ])
            ->addField('mapDefaultLat', FloatType::class, [
                'label' => __('Map default latitude', 'wpcore'),
                'required' => true,
            ])
            ->addField('mapDefaultLng', FloatType::class, [
                'label' => __('Map default longitude', 'wpcore'),
                'required' => true,
            ])
            ->addField('mapDefaultZoom', FloatType::class, [
                'label' => __('Map default zoom', 'wpcore'),
                'required' => true,
            ])
            ->addField('submit', SubmitType::class, [
                'label' => __('Save', 'wpcore'),
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
