<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\IntegerAutocompleteType;
use ScoutUnitsList\Form\Field\SelectType;
use ScoutUnitsList\Form\Field\StringHiddenType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Manager\TypesManager;
use ScoutUnitsList\Model\Attachment;
use ScoutUnitsList\Model\Config;
use ScoutUnitsList\Model\Unit;
use ScoutUnitsList\Validator\UnitValidator;

/**
 * Unit admin form
 */
class UnitAdminForm extends Form
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
        /** @var TypesManager $typesManager */
        $typesManager = $settings['typesManager'];

        $this
            ->addField('type', SelectType::class, [
                'attr' => [
                    'style' => 'width:15em',
                ],
                'label' => __('Type', 'scout-units-list'),
                'options' => $typesManager->getTypes(),
                'required' => true,
            ])
            ->addField('subtype', SelectType::class, [
                'attr' => [
                    'style' => 'width:15em',
                ],
                'label' => __('Subtype', 'scout-units-list'),
                'options' => $typesManager->getSubtypes(),
            ])
            ->addField('parentId', IntegerAutocompleteType::class, [
                'action' => 'sul_units',
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Parent unit', 'scout-units-list'),
                'valueLabel' => is_object($settings['parentUnit']) && $settings['parentUnit'] instanceof Unit ?
                    $settings['parentUnit']->getName() : null,
            ]);
        if ($config->areOrderCategoriesDefined()) {
            $this
                ->addField('orderId', IntegerAutocompleteType::class, [
                    'action' => 'sul_orders',
                    'attr' => [
                        'class' => 'regular-text',
                    ],
                    'label' => __('Order number', 'scout-units-list'),
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
                    'label' => __('Order number', 'scout-units-list'),
                    'required' => true,
                ]);
        }
        $this
            ->addField('name', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Name short', 'scout-units-list'),
                'required' => true,
            ])
            ->addField('nameFull', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Name full', 'scout-units-list'),
                'nullable' => true,
            ])
            ->addField('hero', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Hero short', 'scout-units-list'),
                'nullable' => true,
            ])
            ->addField('heroFull', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Hero full', 'scout-units-list'),
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
