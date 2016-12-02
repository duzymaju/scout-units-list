<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\System\Tools\HelpersTrait;
use ScoutUnitsList\Validator\Condition\ConditionInterface;

/**
 * Form element
 */
abstract class FormElement
{
    use HelpersTrait;

    /** @var mixed */
    private $value;

    /** @var string|null */
    private $viewPath;

    /** @var array */
    private $conditions = [];

    /** @var array */
    private $errors = [];

    /**
     * Add condition
     *
     * @param ConditionInterface $condition condition
     *
     * @return self
     */
    public function addCondition(ConditionInterface $condition)
    {
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param mixed $value value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get view path
     *
     * @return string|null
     */
    protected function getViewPath()
    {
        return $this->viewPath;
    }

    /**
     * Set view path
     *
     * @param string $viewPath view path
     *
     * @return self
     */
    protected function setViewPath($viewPath)
    {
        $this->viewPath = $viewPath;

        return $this;
    }

    /**
     * Validate
     *
     * @return bool
     */
    public function validate()
    {
        $this->errors = [];
        foreach ($this->conditions as $condition) {
            $this->errors = array_merge($this->errors, $condition->check($this->value));
        }

        return !$this->hasErrors();
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Has errors
     *
     * @return bool
     */
    protected function hasErrors()
    {
        $hasErrors = count($this->errors) > 0;

        return $hasErrors;
    }

    /**
     * Clear
     *
     * @return self
     */
    public function clear()
    {
        $this->value = null;
        $this->errors = [];

        return $this;
    }

    /**
     * Render errors
     *
     * @TODO: move to partial
     */
    public function errors()
    {
        if ($this->hasErrors()) {
            echo '<ul class="errors">';
            foreach ($this->getErrors() as $error) {
                echo '<li>' . $this->escape($error) . '</li>';
            }
            echo '</ul>';
        }
    }
}
