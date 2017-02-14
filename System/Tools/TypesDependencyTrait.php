<?php

namespace ScoutUnitsList\System\Tools;

use ScoutUnitsList\Model\Unit;

/**
 * System tools types dependency trait
 */
trait TypesDependencyTrait
{
    /**
     * Get types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            Unit::TYPE_HQ => __('Headquarters', 'scout-units-list'),// główna kwatera
            Unit::TYPE_REGION => __('Region', 'scout-units-list'),// chorągiew
            Unit::TYPE_DISTRICT => __('District', 'scout-units-list'),// hufiec
            Unit::TYPE_GROUP => __('Group', 'scout-units-list'),
            Unit::TYPE_TROOP => __('Troop', 'scout-units-list'),
            Unit::TYPE_PATROL => __('Patrol', 'scout-units-list'),
            Unit::TYPE_SECTION => __('Section', 'scout-units-list'),// agenda
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
    protected static function getSubtypes()
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
     * Get possible parent types
     *
     * @param string $childType child type
     *
     * @return array
     */
    protected static function getPossibleParentTypes($childType)
    {
        $types = [];
        switch ($childType) {
            case Unit::TYPE_CLUB:
                $types[] = Unit::TYPE_DISTRICT;
                $types[] = Unit::TYPE_GROUP;
                $types[] = Unit::TYPE_HQ;
                $types[] = Unit::TYPE_REGION;
                $types[] = Unit::TYPE_TROOP;
                break;

            case Unit::TYPE_DISTRICT:
                $types[] = Unit::TYPE_REGION;
                break;

            case Unit::TYPE_GROUP:
                $types[] = Unit::TYPE_DISTRICT;
                break;

            case Unit::TYPE_PATROL:
                $types[] = Unit::TYPE_DISTRICT;
                $types[] = Unit::TYPE_GROUP;
                $types[] = Unit::TYPE_TROOP;
                break;

            case Unit::TYPE_REGION:
                $types[] = Unit::TYPE_HQ;
                break;

            case Unit::TYPE_SECTION:
                $types[] = Unit::TYPE_DISTRICT;
                $types[] = Unit::TYPE_GROUP;
                $types[] = Unit::TYPE_HQ;
                $types[] = Unit::TYPE_REGION;
                break;

            case Unit::TYPE_TROOP:
                $types[] = Unit::TYPE_DISTRICT;
                $types[] = Unit::TYPE_GROUP;
                break;

            case Unit::TYPE_HQ:
            default:
                // nothing to do
                break;
        }

        return $types;
    }

    /**
     * Get type for subtype
     * 
     * @param string $subtype subtype
     *
     * @return string|null
     */
    protected static function getTypeForSubtype($subtype)
    {
        $subtypes = [
            Unit::SUBTYPE_CUBSCOUTS => Unit::TYPE_TROOP,
            Unit::SUBTYPE_SCOUTS => Unit::TYPE_TROOP,
            Unit::SUBTYPE_SENIORS_COUTS => Unit::TYPE_TROOP,
            Unit::SUBTYPE_ROVERS => Unit::TYPE_TROOP,
            Unit::SUBTYPE_MULTI_LEVEL => Unit::TYPE_TROOP,
            Unit::SUBTYPE_GROUP => Unit::TYPE_GROUP,
            Unit::SUBTYPE_UNION_OF_GROUPS => Unit::TYPE_GROUP,
        ];
        $type = array_key_exists($subtype, $subtypes) ? $subtypes[$subtype] : null;

        return $type;
    }
}
