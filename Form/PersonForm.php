<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\IntegerAutocompleteType;
use ScoutUnitsList\Form\Field\SelectType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Model\Config;
use ScoutUnitsList\Model\User;
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
                'label' => __('User', 'scout-units-list'),
                'required' => true,
                'valueLabel' => is_object($settings['user']) && $settings['user'] instanceof User ?
                    $settings['user']->getNiceName() . ' (' . $settings['user']->getLogin() . ')' : null,
            ])
            ->addField('positionId', SelectType::class, [
                'label' => __('Position', 'scout-units-list'),
                'options' => $settings['positions'],
                'required' => true,
            ])
            ->addField('orderNo', StringType::class, [
                'attr' => [
                    'pattern' => $config->getOrderNoFormat(),
                    'placeholder' => $config->getOrderNoPlaceholder(),
                ],
                'label' => __('Order number', 'scout-units-list'),
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
        return PersonValidator::class;
    }
}
