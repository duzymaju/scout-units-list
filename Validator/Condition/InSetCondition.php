<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator in set condition
 */
class InSetCondition implements ConditionInterface
{
    /** @var array */
    protected $values;

    /**
     * Constructor
     *
     * @param array $set set
     */
    public function __construct(array $set)
    {
        $this->values = array_keys($set);
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
        $errors = array();

        if (!in_array($value, $this->values)) {
            $errors[] = __('This value isn\'t allowed.', 'wpcore');
        }

        return $errors;
    }
}
