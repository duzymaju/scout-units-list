<?php

namespace ScoutUnitsList\Validator\Condition;

use ScoutUnitsList\Model\Repository\Repository;
use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\System\Tools\TypesDependencyTrait;

/**
 * Validator types dependency condition
 */
class TypesDependencyCondition implements ConditionInterface
{
    use TypesDependencyTrait;
    
    /** @var Repository */
    protected $repository;

    /** @var string */
    protected $typeField;

    /** @var string */
    protected $subtypeField;

    /** @var string */
    protected $parentField;

    /**
     * Constructor
     *
     * @param Repository $repository   repository
     * @param string     $typeField    type field
     * @param string     $subtypeField subtype field
     * @param string     $parentField  parent field
     */
    public function __construct(Repository $repository, $typeField, $subtypeField, $parentField)
    {
        $this->repository = $repository;
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
            $errors[] = __('This value should be a ParamPack object - check form/validator configuration.', 'wpcore');
        } else {
            $type = $value->getString($this->typeField);
            $subtype = $value->getString($this->subtypeField);
            $parentId = $value->getInt($this->parentField);

            if (!empty($subtype) && $type != $this->getTypeForSubtype($subtype)) {
                $errors[] = __('Selected type is inadequate for selected subtype.', 'wpcore');
            }

            if (!empty($parentId)) {
                $parentUnit = $this->repository->getOneBy([
                    'id' => $parentId,
                ]);
                if (!isset($parentUnit)) {
                    $errors[] = __('Selected parent unit doesn\'t exist.', 'wpcore');
                } elseif (!in_array($parentUnit->getType(), $this->getPossibleParentTypes($type))) {
                    $errors[] = __('Type of selected parent unit is inadequate for selected type.', 'wpcore');
                }
            }
        }

        return $errors;
    }
}
