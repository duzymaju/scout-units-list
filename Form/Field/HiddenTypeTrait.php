<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\View\Partial;

/**
 * Form hidden field trait
 */
trait HiddenTypeTrait
{
    /**
     * Render widget
     *
     * @param string $partialName partial name
     */
    public function widget($partialName = 'Form/Widget/Hidden')
    {
        $partial = new Partial($this->getViewPath(), $partialName, [
            'attr' => $this->getAttr(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
        ]);
        $partial->render();
    }

    /**
     * Render row
     *
     * @param string $partialName partial name
     */
    public function row($partialName = 'Form/RowWidgetOnly')
    {
        $partial = new Partial($this->getViewPath(), $partialName, [
            'type' => $this,
        ]);
        $partial->render();
    }
}
