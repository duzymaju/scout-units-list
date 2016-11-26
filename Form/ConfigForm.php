<?php

namespace ScoutUnitsList\Form;

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
            ->addField('orderNoFormat', StringType::class, [
                'label' => __('Order number format (for "pattern" attribute)', 'wpcore'),
            ])
            ->addField('orderNoPlaceholder', StringType::class, [
                'label' => __('Order number placeholder', 'wpcore'),
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
