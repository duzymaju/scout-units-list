<?php

namespace ScoutUnitsList\System\Tools;

use ScoutUnitsList\Model\Unit;

/**
 * System tools types dependency trait
 */
trait TypesDependencyTrait
{
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
            case Unit::TYPE_PATROL:
                $types[] = Unit::TYPE_GROUP;
                $types[] = Unit::TYPE_TROOP;
                break;

            case Unit::TYPE_TROOP:
                $types[] = Unit::TYPE_GROUP;
                break;

            case Unit::TYPE_GROUP:
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
