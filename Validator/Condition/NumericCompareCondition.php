<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator numeric compare condition
 */
class NumericCompareCondition implements ConditionInterface
{
    /** @var string */
    protected $methodName;

    /** @var array */
    protected $params = array();

    /**
     * Constructor
     *
     * @param int|float|null $lowerLimit lower limit
     * @param int|float|null $upperLimit upper limit
     * @param bool           $strict     strict
     */
    public function __construct($lowerLimit, $upperLimit, $strict = false)
    {
        if (is_numeric($lowerLimit) && is_numeric($upperLimit)) {
            $this->methodName = $strict ? 'isBetween' : 'isBetweenOrEquals';
            $this->params[] = +$lowerLimit;
            $this->params[] = +$upperLimit;
        } elseif (is_numeric($lowerLimit)) {
            $this->methodName = $strict ? 'isMoreThan' : 'isMoreThanOrEquals';
            $this->params[] = +$lowerLimit;
        } elseif (is_numeric($upperLimit)) {
            $this->methodName = $strict ? 'isLessThan' : 'isLessThanOrEquals';
            $this->params[] = +$upperLimit;
        } else {
            $this->methodName = 'isTrue';
        }
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
        $params = $this->params;
        array_unshift($params, $value);

        $errors = call_user_func_array(array(
            $this,
            $this->methodName,
        ), $params);
        
        return $errors;
    }

    /**
     * Is between
     *
     * @param mixed     $value      value
     * @param int|float $lowerLimit lower limit
     * @param int|float $upperLimit upper limit
     *
     * @return array
     */
    private function isBetween($value, $lowerLimit, $upperLimit)
    {
        $errors = array();

        if ($value <= $lowerLimit || $value >= $upperLimit) {
            $errors[] = $this->sprintf(__('This value should be greater than %n and lower than %n.', 'wpcore'),
                $lowerLimit, $upperLimit);
        }

        return $errors;
    }

    /**
     * Is between or equals
     *
     * @param mixed     $value      value
     * @param int|float $lowerLimit lower limit
     * @param int|float $upperLimit upper limit
     *
     * @return array
     */
    private function isBetweenOrEquals($value, $lowerLimit, $upperLimit)
    {
        $errors = array();

        if ($value < $lowerLimit || $value > $upperLimit) {
            $errors[] = $this->sprintf(__('This value should be between %n and %n.', 'wpcore'), $lowerLimit,
                $upperLimit);
        }

        return $errors;
    }

    /**
     * Is more than
     *
     * @param mixed     $value value
     * @param int|float $limit limit
     *
     * @return array
     */
    private function isMoreThan($value, $limit)
    {
        $errors = array();

        if ($value <= $limit) {
            $errors[] = $this->sprintf(__('This value should be greater than %n.', 'wpcore'), $limit);
        }

        return $errors;
    }

    /**
     * Is more than or equals
     *
     * @param mixed     $value value
     * @param int|float $limit limit
     *
     * @return array
     */
    private function isMoreThanOrEquals($value, $limit)
    {
        $errors = array();

        if ($value < $limit) {
            $errors[] = $this->sprintf(__('This value should be greater than or equal %n.', 'wpcore'), $limit);
        }

        return $errors;
    }

    /**
     * Is less than
     *
     * @param mixed     $value value
     * @param int|float $limit limit
     *
     * @return array
     */
    private function isLessThan($value, $limit)
    {
        $errors = array();

        if ($value >= $limit) {
            $errors[] = $this->sprintf(__('This value should be lower than %n.', 'wpcore'), $limit);
        }

        return $errors;
    }

    /**
     * Is less than or equals
     *
     * @param mixed     $value value
     * @param int|float $limit limit
     *
     * @return array
     */
    private function isLessThanOrEquals($value, $limit)
    {
        $errors = array();

        if ($value > $limit) {
            $errors[] = $this->sprintf(__('This value should be lower than or equal %n.', 'wpcore'), $limit);
        }

        return $errors;
    }

    /**
     * Is true
     *
     * @return array
     */
    private function isTrue()
    {
        $errors = array();

        return $errors;
    }

    /**
     * Get formatted string
     *
     * @param string $format      format
     * @param mixed  ...$argument arguments
     *
     * @return string
     */
    private function sprintf($format)
    {
        $arguments = func_get_args();
        $no = 1;
        $modifiedFormat = preg_replace_callback('#(%[dfns])#', function ($match) use (&$arguments, &$no) {
            if ($match[0] == '%n' && array_key_exists($no, $arguments)) {
                $match[0] = is_int($arguments[$no]) ? '%d' : '%f';
            }
            $no++;
            
            return $match[0];
        }, $format);

        $arguments[0] = $modifiedFormat;
        $text = call_user_func_array('sprintf', $arguments);

        return $text;
    }
}
