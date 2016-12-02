<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\System\View\Partial;

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
        $this->setValue($paramPack->getBool($this->name));

        return $this;
    }

    /**
     * Render widget
     *
     * @param string $partialName partial name
     */
    public function widget($partialName = 'Form/Widget/Boolean')
    {
        $partial = new Partial($this->getViewPath(), $partialName, [
            'attr' => $this->getAttr(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
        ]);
        $partial->render();
    }
}
