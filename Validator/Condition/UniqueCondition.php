<?php

namespace ScoutUnitsList\Validator\Condition;

use ScoutUnitsList\Model\Repository\Repository;
use ScoutUnitsList\System\ParamPack;

/**
 * Validator unique condition
 */
class UniqueCondition implements ConditionInterface
{
    /** @var Repository */
    protected $repository;

    /** @var array */
    protected $fieldNames;

    /**
     * Constructor
     *
     * @param Repository $repository repository
     * @param array      $fieldNames field names
     */
    public function __construct(Repository $repository, array $fieldNames)
    {
        $this->repository = $repository;
        $this->fieldNames = $fieldNames;
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
            $conditions = [];
            foreach ($this->fieldNames as $fieldName => $defaultValue) {
                $fieldValue = $value->get($fieldName, $defaultValue);
                $conditions[$fieldName] = is_numeric($fieldValue) ? +$fieldValue : $fieldValue;
            }
            $duplicate = $this->repository->getOneBy($conditions);
            if (isset($duplicate)) {
                $errors[] = __('This record should be unique.', 'scout-units-list');
            }
        }

        return $errors;
    }
}
