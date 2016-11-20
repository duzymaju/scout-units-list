<?php

namespace ScoutUnitsList\Form\Field;

/**
 * Form hidden field trait
 */
trait HiddenTypeTrait
{
    /**
     * Render widget
     *
     * @TODO: move to partial
     */
    public function widget()
    {
        echo '<input type="hidden" name="' . $this->escape($this->getName()) . '" value="' .
            $this->escape($this->getValue()) . '">';
    }

    /**
     * Render row
     *
     * @TODO: move to partial
     */
    public function row()
    {
        echo '<dd>';
        $this->widget();
        echo '</dd>';
    }
}
