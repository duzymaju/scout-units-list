<?php

namespace ScoutUnitsList\Form\Field;

use ScoutUnitsList\System\View\Partial;

/**
 * Form autocomplete integer field
 */
class IntegerAutocompleteType extends IntegerType
{
    /** @var string|null */
    protected $action;

    /** @var string|null */
    protected $valueLabel;

    /**
     * Setup
     *
     * @param array $settings settings
     */
    protected function setup(array $settings)
    {
        $this->action = array_key_exists('action', $settings) ? $settings['action'] : null;
        $this->valueLabel = array_key_exists('valueLabel', $settings) ? $settings['valueLabel'] : null;
    }

    /**
     * Render widget
     *
     * @param string $partialName partial name
     */
    public function widget($partialName = 'Form/Widget/IntegerAutocomplete')
    {
        if (empty($this->action)) {
            parent::widget();
        } else {
            $partial = new Partial($this->getViewPath(), $partialName, [
                'action' => $this->action,
                'filled' => isset($this->valueLabel) && $this->getValue() !== null,
                'name' => $this->getName(),
                'value' => $this->getValue(),
                'valueLabel' => $this->valueLabel,
            ]);
            $partial->render();
        }
    }
}
