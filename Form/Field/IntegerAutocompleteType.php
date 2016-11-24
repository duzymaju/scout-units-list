<?php

namespace ScoutUnitsList\Form\Field;

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
     * @TODO: move to partial
     */
    public function widget()
    {
        if (empty($this->action)) {
            parent::widget();
        } else {
            echo '<div class="autocomplete-box' . (isset($this->valueLabel) ? ' autocomplete-filled' : '') . '">' .
                '<input type="text"> ' .
                '<input type="hidden" data-autocomplete-action="' . $this->escape($this->action) . '" name="' .
                $this->escape($this->getName()) . '" value="' . $this->escape($this->getValue()) . '"> ' .
                '<span class="autocomplete-value">' .
                    (isset($this->valueLabel) ? $this->escape($this->valueLabel) : '') . '</span>' .
                '</div>';
        }
    }
}
