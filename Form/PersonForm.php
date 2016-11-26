<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\IntegerAutocompleteType;
use ScoutUnitsList\Form\Field\SelectType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Model\Config;
use ScoutUnitsList\Validator\PersonValidator;

/**
 * Person form
 */
class PersonForm extends Form
{
    /**
     * Set fields
     *
     * @param array $settings settings
     */
    protected function setFields(array $settings)
    {
        /** @var Config $config */
        $config = $settings['config'];

        $this
            ->addField('userId', IntegerAutocompleteType::class, [
                'action' => 'sul_users',
                'label' => __('User ID', 'wpcore'),
                'required' => true,
            ])
            ->addField('positionId', SelectType::class, [
                'label' => __('Position', 'wpcore'),
                'options' => $settings['positions'],
                'required' => true,
            ])
            ->addField('orderNo', StringType::class, [
                'attr' => [
                    'pattern' => $config->getOrderNoFormat(),
                    'placeholder' => $config->getOrderNoPlaceholder(),
                ],
                'label' => __('Order number', 'wpcore'),
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
        return PersonValidator::class;
    }
}
