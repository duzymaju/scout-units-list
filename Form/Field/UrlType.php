<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\View\Partial;

/**
 * Form URL field
 */
class UrlType extends StringType
{
    /**
     * Render widget
     *
     * @param string $partialName partial name
     */
    public function widget($partialName = 'Form/Widget/Url')
    {
        $partial = new Partial($this->getViewPath(), $partialName, [
            'attr' => $this->getAttr(),
            'name' => $this->getName(),
            'required' => $this->isRequired(),
            'value' => $this->getValue(),
        ]);
        $partial->render();
    }
}
