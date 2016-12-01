<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator callback condition
 */
class CallbackCondition implements ConditionInterface
{
    /** @var type */
    protected $callback;

    /**
     * Constructor
     * 
     * @param type $callback callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;
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
        $callback = $this->callback;
        $result = $callback($value);

        if (is_array($result)) {
            $errors = $result;
        } else {
            $errors = [];
            if (!$result) {
                $errors[] = __('This value is inproper.', 'wpcore');
            }
        }

        return $errors;
    }
}
