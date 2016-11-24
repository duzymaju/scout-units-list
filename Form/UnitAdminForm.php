<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\IntegerAutocompleteType;
use ScoutUnitsList\Form\Field\IntegerType;
use ScoutUnitsList\Form\Field\SelectType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Model\Config;
use ScoutUnitsList\Model\Unit;
use ScoutUnitsList\Validator\UnitValidator;

/**
 * Unit admin form
 */
class UnitAdminForm extends BasicForm
{
    /**
     * Get types
     *
     * @return array
     */
    public function getTypes()
    {
        return [
            Unit::TYPE_GROUP => __('Group', 'wpcore'),
            Unit::TYPE_TROOP => __('Troop', 'wpcore'),
            Unit::TYPE_PATROL => __('Patrol', 'wpcore'),
            Unit::TYPE_CLUB => __('Club', 'wpcore'),
        ];
    }

    /**
     * Get subtypes
     *
     * @return array
     */
    private function getSubtypes()
    {
        return [
            Unit::SUBTYPE_CUBSCOUTS => __('Cubscouts', 'wpcore'),
            Unit::SUBTYPE_SCOUTS => __('Scouts', 'wpcore'),
            Unit::SUBTYPE_SENIORS_COUTS => __('Senior scouts', 'wpcore'),
            Unit::SUBTYPE_ROVERS => __('Rovers', 'wpcore'),
            Unit::SUBTYPE_MULTI_LEVEL => __('Multi level', 'wpcore'),
            Unit::SUBTYPE_GROUP => __('Group', 'wpcore'),
            Unit::SUBTYPE_UNION_OF_GROUPS => __('Union of troops', 'wpcore'),
        ];
    }

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
                'label' => __('Status', 'wpcore'),
                'options' => [
                    Unit::STATUS_ACTIVE => __('Active', 'wpcore'),
                    Unit::STATUS_HIDDEN => __('Hidden', 'wpcore'),
                ],
                'required' => true,
            ])
            ->addField('type', SelectType::class, [
                'label' => __('Type', 'wpcore'),
                'options' => self::getTypes(),
                'required' => true,
            ])
            ->addField('subtype', SelectType::class, [
                'label' => __('Subtype', 'wpcore'),
                'options' => self::getSubtypes(),
            ])
            ->addField('sort', IntegerType::class, [
                'label' => __('Sort', 'wpcore'),
            ])
            ->addField('parentId', IntegerAutocompleteType::class, [
                'action' => 'sul_units',
                'label' => __('Parent', 'wpcore'),
                'valueLabel' => is_object($settings['parentUnit']) && $settings['parentUnit'] instanceof Unit ?
                    $settings['parentUnit']->getName() : null,
            ])
            ->addField('orderNo', StringType::class, [
                'attr' => [
                    'pattern' => $config->getOrderNoFormat(),
                    'placeholder' => $config->getOrderNoPlaceholder(),
                ],
                'label' => __('Order number', 'wpcore'),
                'required' => true,
            ])
            ->addField('name', StringType::class, [
                'label' => __('Name short', 'wpcore'),
                'required' => true,
            ])
            ->addField('nameFull', StringType::class, [
                'label' => __('Name full', 'wpcore'),
            ])
            ->addField('hero', StringType::class, [
                'label' => __('Hero short', 'wpcore'),
            ])
            ->addField('heroFull', StringType::class, [
                'label' => __('Hero full', 'wpcore'),
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
        return UnitValidator::class;
    }
}
