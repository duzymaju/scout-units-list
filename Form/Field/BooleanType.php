<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\ParamPack;

/**
 * Form boolean field
 */
class BooleanType extends BasicType
{
    /**
     * Set value from param pack
     *
     * @param ParamPack $paramPack param pack
     *
     * @return self
     */
    public function setValueFromParamPack(ParamPack $paramPack)
    {
        $this->setValue($paramPack->getBool($this->name));

        return $this;
    }

    /**
     * Render widget
     *
     * @TODO: move to partial
     */
    public function widget()
    {
        echo '<select name="' . $this->escape($this->getName()) . '"' . $this->getAttr() . '>';
        echo '<option value="1"' . ($this->getValue() ? ' selected' : '') . '>' . __('Yes', 'wpcore') . '</option>';
        echo '<option value="0"' . (!$this->getValue() ? ' selected' : '') . '>' . __('No', 'wpcore') . '</option>';
        echo '</select>';
    }
}
