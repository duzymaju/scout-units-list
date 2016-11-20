<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\IntegerType;
use ScoutUnitsList\Form\Field\SelectType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Model\Config;
use ScoutUnitsList\Validator\PersonValidator;

/**
 * Person form
 */
class PersonForm extends BasicForm
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
            ->addField('userId', IntegerType::class, array(
                'label' => __('User ID', 'wpcore'),
                'required' => true,
            ))
            ->addField('positionId', SelectType::class, array(
                'label' => __('Position', 'wpcore'),
                'options' => $settings['positions'],
                'required' => true,
            ))
            ->addField('orderNo', StringType::class, array(
                'attr' => [
                    'pattern' => $config->getOrderNoFormat(),
                    'placeholder' => $config->getOrderNoPlaceholder(),
                ],
                'label' => __('Order number', 'wpcore'),
                'required' => true,
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
        return PersonValidator::class;
    }
}
