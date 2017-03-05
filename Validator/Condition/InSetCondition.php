<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator in set condition
 */
class InSetCondition implements ConditionInterface
{
    /** @var array */
    protected $values;

    /** @var bool */
    protected $isRequired;

    /**
     * Constructor
     *
     * @param array $values     values
     * @param bool  $isRequired is required
     */
    public function __construct(array $values, $isRequired = true)
    {
        $this->values = $values;
        $this->isRequired = $isRequired;
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

        $errorText = __('This value isn\'t allowed.', 'scout-units-list');
        if (is_array($value)) {
            foreach ($value as $subValue) {
                if (!in_array($subValue, $this->values)) {
                    $errors[] = $errorText;
                    break;
                }
            }
        } elseif (!in_array($value, $this->values) && ($this->isRequired || !empty($value))) {
            $errors[] = $errorText;
        }

        return $errors;
    }
}
