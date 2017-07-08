<?php

namespace ScoutUnitsList\Form\Field;

/**
 * Form multiline string field
 */
class StringMultilineType extends StringType
{
    /**
     * Render widget
     *
     * @param string $partialName partial name
     */
    public function widget($partialName = 'Form/Widget/Textarea')
    {
        parent::widget($partialName);
    }
}
