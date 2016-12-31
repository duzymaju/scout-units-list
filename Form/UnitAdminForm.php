<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\IntegerAutocompleteType;
use ScoutUnitsList\Form\Field\IntegerType;
use ScoutUnitsList\Form\Field\SelectType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
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
     * Get types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            Unit::TYPE_GROUP => __('Group', 'scout-units-list'),
            Unit::TYPE_TROOP => __('Troop', 'scout-units-list'),
            Unit::TYPE_PATROL => __('Patrol', 'scout-units-list'),
            Unit::TYPE_CLUB => __('Club', 'scout-units-list'),
        ];
    }

    /**
     * Get type name
     *
     * @return string|null
     */
    public static function getTypeName($type)
    {
        $types = self::getTypes();
        $typeName = array_key_exists($type, $types) ? $types[$type] : null;

        return $typeName;
    }

    /**
     * Get subtypes
     *
     * @return array
     */
    private static function getSubtypes()
    {
        $subtypes = [
            Unit::SUBTYPE_CUBSCOUTS => __('Cubscouts', 'scout-units-list'),
            Unit::SUBTYPE_SCOUTS => __('Scouts', 'scout-units-list'),
            Unit::SUBTYPE_SENIORS_COUTS => __('Senior scouts', 'scout-units-list'),
            Unit::SUBTYPE_ROVERS => __('Rovers', 'scout-units-list'),
            Unit::SUBTYPE_MULTI_LEVEL => __('Multi level', 'scout-units-list'),
            Unit::SUBTYPE_GROUP => __('Group', 'scout-units-list'),
            Unit::SUBTYPE_UNION_OF_GROUPS => __('Union of troops', 'scout-units-list'),
        ];
        foreach ($subtypes as $subtype => $name) {
            $subtypes[$subtype] = [
                'attr' => [
                    'data-for-type' => self::getTypeForSubtype($subtype),
                ],
                'name' => $name,
            ];
        }

        return $subtypes;
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
            ])
            ->addField('orderNo', StringType::class, [
                'attr' => [
                    'class' => 'regular-text',
                    'pattern' => $config->getOrderNoFormat(),
                    'placeholder' => $config->getOrderNoPlaceholder(),
                ],
                'label' => __('Order number', 'scout-units-list'),
                'required' => true,
            ])
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
