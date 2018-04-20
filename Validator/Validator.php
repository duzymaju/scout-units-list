<?php

namespace ScoutUnitsList\Validator;

use ScoutUnitsList\Form\Field\BasicType as Field;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Form;

/**
 * Validator
 */
abstract class Validator
{
    /** @var Form */
    private $form;

    /**
     * Set up
     *
     * @param Form  $form     form
     * @param array $settings settings
     *
     * @return self
     */
    public function setUp(Form $form, array $settings = [])
    {
        $this->form = $form;
        $this->setConditions($settings);

        return $this;
    }

    /**
     * Set conditions
     *
     * @param array $settings settings
     */
    abstract protected function setConditions(array $settings);

    /**
     * Get form
     *
     * @return Form
     */
    protected function getForm()
    {
        return $this->form;
    }

    /**
     * Get field
     * 
     * @param string $name name
     *
     * @return Field
     */
    protected function getField($name)
    {
        $fields = $this->form->getFields();
        // @TODO: return null if field doesn't exist when YAML validation files will be ready
        $field = array_key_exists($name, $fields) ? $fields[$name] : new StringType($name, '');

        return $field;
    }

    /**
     * Validate
     * 
     * @return bool
     */
    public function validate()
    {
        $isValid = true;
        if (!$this->form->validate()) {
            $isValid = false;
        }
        foreach ($this->form->getFields() as $field) {
            if (!$field->validate()) {
                $isValid = false;
            }
        }

        return $isValid;
    }
}
