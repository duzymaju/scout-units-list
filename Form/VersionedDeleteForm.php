<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\IntegerAutocompleteType;
use ScoutUnitsList\Form\Field\StringHiddenType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Model\Attachment;
use ScoutUnitsList\Model\Config;
use ScoutUnitsList\Validator\VersionedDeleteValidator;

/**
 * Versioned delete form
 */
class VersionedDeleteForm extends Form
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

        if ($config->isOrderCategoryDefined()) {
            $this
                ->addField('orderId', IntegerAutocompleteType::class, [
                    'action' => 'sul_orders',
                    'attr' => [
                        'class' => 'regular-text',
                    ],
                    'label' => __('Order no', 'scout-units-list'),
                    'required' => true,
                    'valueField' => 'input[name="orderNo"]',
                    'valueLabel' => array_key_exists('order', $settings) && is_object($settings['order']) &&
                        $settings['order'] instanceof Attachment ? $settings['order']->getTitle() : null,
                ])
                ->addField('orderNo', StringHiddenType::class, [
                    'required' => true,
                ]);
        } else {
            $this
                ->addField('orderNo', StringType::class, [
                    'attr' => [
                        'class' => 'regular-text',
                        'pattern' => $config->getOrderNoFormat(),
                        'placeholder' => $config->getOrderNoPlaceholder(),
                    ],
                    'label' => __('Order no', 'scout-units-list'),
                    'required' => true,
                ]);
        }
        $this
            ->addField('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'button button-primary alignright',
                ],
                'label' => __('Delete', 'scout-units-list'),
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
        return VersionedDeleteValidator::class;
    }
}
