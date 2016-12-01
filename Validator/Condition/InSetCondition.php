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
     * @param array $values values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
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

        if (!in_array($value, $this->values)) {
            $errors[] = __('This value isn\'t allowed.', 'wpcore');
        }

        return $errors;
    }
}
