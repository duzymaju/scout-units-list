<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\Validator\Condition\InSetCondition;

/**
 * Form select field
 */
class SelectType extends BasicType
{
    /** @var array */
    protected $options;

    /**
     * Setup
     *
     * @param array $settings settings
     */
    protected function setup(array $settings)
    {
        if (array_key_exists('options', $settings)) {
            $this->options = $settings['options'];
            if (!$settings['required']) {
                $options = [
                    '' => '',
                ];
                $this->options = array_merge($options, $this->options);
            }

            $this->addCondition(new InSetCondition($this->options));
        }
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
        $value = $paramPack->getString($this->name);

        if (is_numeric($value)) {
            $numericValue = +$value;
            $values = array_keys($this->options);
            if (!in_array($value, $values) && in_array($numericValue, $values)) {
                $value = $numericValue;
            }
        } elseif (empty($value)) {
            $value = null;
        }

        $this->setValue($value);

        return $this;
    }

    /**
     * Render widget
     *
     * @TODO: move to partial
     */
    public function widget()
    {
        echo '<select name="' . $this->escape($this->name) . '"' . $this->getAttr() . '>';
        foreach ($this->options as $key => $value) {
            echo '<option value="' . $this->escape($key) . '"' . ($key == $this->getValue() ? ' selected' : '') . '>' .
                $this->escape($value) . '</option>';
        }
        echo '</select>';
    }
}
