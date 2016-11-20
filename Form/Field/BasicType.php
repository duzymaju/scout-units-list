<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\System\Tools\HelpersTrait;
use ScoutUnitsList\Validator\Condition\ConditionInterface;
use ScoutUnitsList\Validator\Condition\NotEmptyCondition;

/**
 * Form basic field
 */
abstract class BasicType
{
    use HelpersTrait;

    /** @var string */
    protected $name;

    /** @var string */
    protected $label;

    /** @var array */
    protected $attr;

    /** @var mixed */
    protected $value;

    /** @var bool */
    protected $isRequired;

    /** @var array */
    private $conditions = [];

    /** @var array */
    private $errors = [];

    /**
     * Constructor
     *
     * @param string $name     name
     * @param array  $settings settings
     */
    public function __construct($name, array $settings = [])
    {
        $this->name = $name;
        $this->label = array_key_exists('label', $settings) ? $settings['label'] : $name;
        $this->attr = array_key_exists('attr', $settings) && is_array($settings['attr']) ? $settings['attr'] : [];

        if (!array_key_exists('required', $settings) || !is_bool($settings['required'])) {
            $settings['required'] = false;
        }
        $this->isRequired = $settings['required'];
        if ($this->isRequired) {
            $this->addCondition(new NotEmptyCondition());
        }

        $this->setup($settings);
    }

    /**
     * Setup
     */
    protected function setup()
    {
    }

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
     * Set value from param pack
     *
     * @param ParamPack $paramPack param pack
     *
     * @return self
     */
    public function setValueFromParamPack(ParamPack $paramPack)
    {
        $this->value = $paramPack->get($this->name);

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

        return $this->isValid();
    }

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        $valid = count($this->errors) == 0 || false;

        return $valid;
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
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
     * Is required
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->isRequired;
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
     * Get attributes as string
     * 
     * @return string
     */
    public function getAttr()
    {
        $list = [];
        foreach ($this->attr as $key => $value) {
            if (isset($value)) {
                $list[] = $this->escape($key) . '="' . $this->escape($value) . '"';
            }
        }
        $string = count($list) > 0 ? ' ' . implode(' ', $list) : '';

        return $string;
    }

    /**
     * Render row
     *
     * @TODO: move to partial
     */
    public function row()
    {
        $this->label();
        echo '<dd>';
        $this->errors();
        $this->widget();
        echo '</dd>';
    }

    /**
     * Render label
     *
     * @TODO: move to partial
     */
    public function label()
    {
        echo '<dt>' . $this->escape($this->getLabel()) . ($this->isRequired() ? ' *' : '') . ':</dt>';
    }

    /**
     * Render errors
     *
     * @TODO: move to partial
     */
    public function errors()
    {
        if (!$this->isValid()) {
            echo '<ul class="errors">';
            foreach ($this->getErrors() as $error) {
                echo '<li>' . $this->escape($error) . '</li>';
            }
            echo '</ul>';
        }
    }

    /**
     * Render widget
     *
     * @TODO: move to partial
     */
    abstract public function widget();
}
