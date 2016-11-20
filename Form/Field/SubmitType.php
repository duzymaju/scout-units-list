<?php

namespace ScoutUnitsList\Form\Field;

/**
 * Form submit field
 */
class SubmitType extends BasicType
{
    /**
     * Render widget
     *
     * @TODO: move to partial
     */
    public function widget()
    {
        echo '<input type="submit" name="' . $this->escape($this->getName()) . '" value="' .
            $this->escape($this->getLabel()) . '">';
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
