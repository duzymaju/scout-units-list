<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator string length condition
 */
class StringLengthCondition implements ConditionInterface
{
    /** @var int|null */
    protected $minLength;

    /** @var int|null */
    protected $maxLength;

    /**
     * Constructor
     *
     * @param int|null $maxLength maximum length
     * @param int|null $minLength minimum length
     */
    public function __construct($maxLength, $minLength = null)
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
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

        $length = mb_strlen($value);
        if (isset($this->minLength) && $length < $this->minLength) {
            $errors[] = sprintf(__('This value should be longer than %d characters.', 'wpcore'), $this->minLength);
        }
        if (isset($this->maxLength) && $length > $this->maxLength) {
            $errors[] = sprintf(__('This value should be shorter than %d characters.', 'wpcore'), $this->maxLength);
        }

        return $errors;
    }
}
