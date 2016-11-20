<?php

namespace ScoutUnitsList\Form\Field;

/**
 * Form URL field
 */
class UrlType extends StringType
{
    /**
     * Render widget
     *
     * @TODO: move to partial
     */
    public function widget()
    {
        echo '<input type="url" name="' . $this->escape($this->getName()) . '" value="' .
            $this->escape($this->getValue()) . '"' . ($this->isRequired() ? ' required' : '') . $this->getAttr() . '>';
    }
}
