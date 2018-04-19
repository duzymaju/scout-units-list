<?php

namespace ScoutUnitsList\Validator\Condition;

use ScoutUnitsList\Manager\TypesManager;
use ScoutUnitsList\Model\Repository\UnitRepository;
use ScoutUnitsList\System\ParamPack;

/**
 * Validator types dependency condition
 */
class TypesDependencyCondition implements ConditionInterface
{
    /** @var UnitRepository */
    protected $unitRepository;

    /** @var TypesManager */
    protected $typesManager;

    /** @var string */
    protected $typeField;

    /** @var string */
    protected $subtypeField;

    /** @var string */
    protected $parentField;

    /**
     * Constructor
     *
     * @param UnitRepository $unitRepository unit repository
     * @param TypesManager   $typesManager   types manager
     * @param string         $typeField      type field
     * @param string         $subtypeField   subtype field
     * @param string         $parentField    parent field
     */
    public function __construct(UnitRepository $unitRepository, TypesManager $typesManager, $typeField, $subtypeField,
        $parentField)
    {
        $this->unitRepository = $unitRepository;
        $this->typesManager = $typesManager;
        $this->typeField = $typeField;
        $this->subtypeField = $subtypeField;
        $this->parentField = $parentField;
    }

    /**
     * Check
     *
     * @param mixed $value value
     *
     * @return array
     */
    public function check($value)
    {
        $errors = [];

        if (!$value instanceof ParamPack) {
            $errors[] = __('This value should be a ParamPack object - check form/validator configuration.',
                'scout-units-list');
        } else {
            $type = $value->getString($this->typeField);
            $subtype = $value->getString($this->subtypeField);
            $parentId = $value->getInt($this->parentField);

            if (!empty($subtype) && $type != $this->typesManager->getTypeForSubtype($subtype)) {
                $errors[] = __('Selected type is inadequate for selected subtype.', 'scout-units-list');
            }

            if (!empty($parentId)) {
                $parentUnit = $this->unitRepository->getOneBy([
                    'id' => $parentId,
                ]);
                if (!isset($parentUnit)) {
                    $errors[] = __('Selected parent unit doesn\'t exist.', 'scout-units-list');
                } elseif (!in_array($parentUnit->getType(), $this->typesManager->getPossibleParentTypes($type))) {
                    $errors[] = __('Type of selected parent unit is inadequate for selected type.', 'scout-units-list');
                }
            }
        }

        return $errors;
    }
}
