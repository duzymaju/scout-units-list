<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\View\Partial;

/**
 * Form e-mail field
 */
class EmailType extends StringType
{
    /**
     * Render widget
     *
     * @param string $partialName partial name
     */
    public function widget($partialName = 'Form/Widget/Email')
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
