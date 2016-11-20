<?php

namespace ScoutUnitsList\Form\Field;

/**
 * Form e-mail field
 */
class EmailType extends StringType
{
    /**
     * Render widget
     *
     * @TODO: move to partial
     */
    public function widget()
    {
        echo '<input type="email" name="' . $this->escape($this->getName()) . '" value="' .
            $this->escape($this->getValue()) . '"' . ($this->isRequired() ? ' required' : '') . '>';
    }
}
