<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\Validator\Condition\FloatCondition;

/**
 * Form float field
 */
class FloatType extends BasicType
{
    /**
     * Setup
     */
    protected function setup()
    {
        $this->addCondition(new FloatCondition());
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
        $this->value = $paramPack->getFloat($this->name);

        return $this;
    }

    /**
     * Render widget
     *
     * @TODO: move to partial
     */
    public function widget()
    {
        echo '<input type="text" name="' . $this->escape($this->getName()) . '" value="' .
            $this->escape($this->getValue()) . '" pattern="^-?[0-9]+(\.[0-9]+)?$"' .
            ($this->isRequired() ? ' required' : '') . '>';
    }
}
