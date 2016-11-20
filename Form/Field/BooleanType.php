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
        $this->value = $paramPack->getBool($this->name);

        return $this;
    }

    /**
     * Render widget
     *
     * @TODO: move to partial
     */
    public function widget()
    {
        echo '<select name="' . $this->escape($this->getName()) . '">';
        echo '<option value="1"' . ($this->getValue() ? ' selected' : 'wpcore') . '>' . __('Yes', '') . '</option>';
        echo '<option value="0"' . (!$this->getValue() ? ' selected' : 'wpcore') . '>' . __('No', '') . '</option>';
        echo '</select>';
    }
}
