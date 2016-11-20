<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\IntegerType;
use ScoutUnitsList\Form\Field\SelectType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
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
     */
    protected function setFields()
    {
        $this
            ->addField('status', SelectType::class, array(
                'label' => __('Status', 'wpcore'),
                'options' => array(
                    Unit::STATUS_ACTIVE => __('Active', 'wpcore'),
                    Unit::STATUS_HIDDEN => __('Hidden', 'wpcore'),
                ),
                'required' => true,
            ))
            ->addField('type', SelectType::class, array(
                'label' => __('Type', 'wpcore'),
                'options' => self::getTypes(),
                'required' => true,
            ))
            ->addField('subtype', SelectType::class, array(
                'label' => __('Subtype', 'wpcore'),
                'options' => self::getSubtypes(),
            ))
            ->addField('sort', IntegerType::class, array(
                'label' => __('Sort', 'wpcore'),
            ))
            ->addField('parentId', IntegerType::class, array(
                'label' => __('Parent', 'wpcore'),
            ))
            ->addField('orderNo', StringType::class, array(
                'label' => __('Order number', 'wpcore'),
                'required' => true,
            ))
            ->addField('name', StringType::class, array(
                'label' => __('Name short', 'wpcore'),
                'required' => true,
            ))
            ->addField('nameFull', StringType::class, array(
                'label' => __('Name full', 'wpcore'),
            ))
            ->addField('hero', StringType::class, array(
                'label' => __('Hero short', 'wpcore'),
            ))
            ->addField('heroFull', StringType::class, array(
                'label' => __('Hero full', 'wpcore'),
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
        return UnitValidator::class;
    }
}
