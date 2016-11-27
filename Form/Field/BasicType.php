<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\Form\FormElement;
use ScoutUnitsList\Validator\Condition\NotEmptyCondition;

/**
 * Form basic field
 */
abstract class BasicType extends FormElement
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $label;

    /** @var array */
    protected $attr;

    /** @var bool */
    protected $isRequired;

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
     * Set value from param pack
     *
     * @param ParamPack $paramPack param pack
     *
     * @return self
     */
    public function setValueFromParamPack(ParamPack $paramPack)
    {
        $this->setValue($paramPack->get($this->name));

        return $this;
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
     * Is required
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->isRequired;
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
     * Render widget
     *
     * @TODO: move to partial
     */
    abstract public function widget();
}
