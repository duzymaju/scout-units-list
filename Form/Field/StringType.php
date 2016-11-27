<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\Validator\Condition\StringCondition;

/**
 * Form string field
 */
class StringType extends BasicType
{
    /**
     * Setup
     */
    protected function setup()
    {
        $this->addCondition(new StringCondition());
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
        $this->setValue($paramPack->getString($this->name));

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
            $this->escape($this->getValue()) . '"' . ($this->isRequired() ? ' required' : '') . $this->getAttr() . '>';
    }
}
