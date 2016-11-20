<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator regular expression condition
 */
class RegExpCondition implements ConditionInterface
{
    /** @var string */
    protected $regExp;

    /**
     * Constructor
     *
     * @param string $regExp regular expression
     */
    public function __construct($regExp)
    {
        $this->regExp = $regExp;
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

        if (!preg_match($this->regExp, $value)) {
            $errors[] = __('This value has incorrect format.', 'wpcore');
        }

        return $errors;
    }
}
