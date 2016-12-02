<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\View\Partial;

/**
 * Form submit field
 */
class SubmitType extends BasicType
{
    /**
     * Render widget
     *
     * @param string $partialName partial name
     */
    public function widget($partialName = 'Form/Widget/Submit')
    {
        $partial = new Partial($this->getViewPath(), $partialName, [
            'attr' => $this->getAttr(),
            'label' => $this->getLabel(),
            'name' => $this->getName(),
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
