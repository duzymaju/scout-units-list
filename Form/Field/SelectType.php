<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\System\View\Partial;
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
        $this->setupSelect($settings);
        if (is_array($this->options)) {
            $this->addCondition(new InSetCondition(array_keys($this->options), $settings['required']));
        }
    }

    /**
     * Setup select
     *
     * @param array $settings settings
     */
    protected function setupSelect(array $settings)
    {
        if (array_key_exists('options', $settings)) {
            $this->options = $settings['options'];
            if (!$settings['required'] && !$this->isMultiple()) {
                $this->options = [
                    '' => '',
                ] + $this->options;
            }
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
        $value = $paramPack->get($this->name);

        if (is_numeric($value)) {
            $value = $this->typeToInteger($value);
        } elseif (is_array($value)) {
            foreach ($value as $key => $subValue) {
                if (is_numeric($subValue)) {
                    $value[$key] = $this->typeToInteger($subValue);
                }
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
     * @param string $partialName partial name
     */
    public function widget($partialName = 'Form/Widget/Select')
    {
        $partial = new Partial($this->getViewPath(), $partialName, [
            'attr' => $this->getAttr(),
            'isMultiple' => $this->isMultiple(),
            'name' => $this->getName(),
            'options' => $this->options,
            'value' => $this->getValue(),
        ]);
        $partial->render();
    }

    /**
     * Type to integer
     *
     * @param string $value value
     *
     * @return int|string
     */
    private function typeToInteger($value)
    {
        $numericValue = +$value;
        $values = array_keys($this->options);
        if (!in_array($value, $values) && in_array($numericValue, $values)) {
            $value = $numericValue;
        }

        return $value;
    }

    /**
     * Is multiple
     *
     * @return bool
     */
    private function isMultiple()
    {
        return isset($this->attr['multiple']) && $this->attr['multiple'];
    }
}
