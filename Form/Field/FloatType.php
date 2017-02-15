<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\System\View\Partial;
use ScoutUnitsList\Validator\Condition\FloatCondition;

/**
 * Form float field
 */
class FloatType extends BasicType
{
    /**
     * Setup
     */
    protected function setup()
    {
        $this->addCondition(new FloatCondition());
    }

    /**
     * Set value from param pack
     *
     * @param ParamPack $paramPack param pack
     *
     * @return self
     */
    public function setValueFromParamPack(ParamPack $paramPack)
    {
        $this->setValue($this->filterNullable($paramPack->getFloat($this->name)));

        return $this;
    }

    /**
     * Render widget
     *
     * @param string $partialName partial name
     */
    public function widget($partialName = 'Form/Widget/Float')
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
