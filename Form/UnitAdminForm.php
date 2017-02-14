<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\IntegerAutocompleteType;
use ScoutUnitsList\Form\Field\IntegerType;
use ScoutUnitsList\Form\Field\SelectType;
use ScoutUnitsList\Form\Field\StringHiddenType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Model\Attachment;
use ScoutUnitsList\Model\Config;
use ScoutUnitsList\Model\Unit;
use ScoutUnitsList\System\Tools\TypesDependencyTrait;
use ScoutUnitsList\Validator\UnitValidator;

/**
 * Unit admin form
 */
class UnitAdminForm extends Form
{
    use TypesDependencyTrait;

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
            ->addField('status', SelectType::class, [
                'attr' => [
                    'style' => 'width:15em',
                ],
                'label' => __('Status', 'scout-units-list'),
                'options' => [
                    Unit::STATUS_ACTIVE => __('Active', 'scout-units-list'),
                    Unit::STATUS_HIDDEN => __('Hidden', 'scout-units-list'),
                ],
                'required' => true,
            ])
            ->addField('type', SelectType::class, [
                'attr' => [
                    'style' => 'width:15em',
                ],
                'label' => __('Type', 'scout-units-list'),
                'options' => self::getTypes(),
                'required' => true,
            ])
            ->addField('subtype', SelectType::class, [
                'attr' => [
                    'style' => 'width:15em',
                ],
                'label' => __('Subtype', 'scout-units-list'),
                'options' => self::getSubtypes(),
            ])
            ->addField('sort', IntegerType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Sort', 'scout-units-list'),
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
        if ($config->isOrderCategoryDefined()) {
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
            ])
            ->addField('hero', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Hero short', 'scout-units-list'),
            ])
            ->addField('heroFull', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                ],
                'label' => __('Hero full', 'scout-units-list'),
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
