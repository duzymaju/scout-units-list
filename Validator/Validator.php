<?php

namespace ScoutUnitsList\Validator;

use ScoutUnitsList\Form\Field\BasicType as Field;
use ScoutUnitsList\Form\Field\StringType;

/**
 * Basic validator
 */
abstract class Validator
{
    /** @var Field[] */
    private $fields = [];

    /**
     * Constructor
     *
     * @param Field[] $fields fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
        $this->setConditions();
    }

    /**
     * Set conditions
     */
    abstract protected function setConditions();

    /**
     * Get field
     * 
     * @param string $name name
     *
     * @return Field
     */
    protected function getField($name)
    {
        // @TODO: return null if field doesn't exist when YAML validation files will be ready
        $field = array_key_exists($name, $this->fields) ? $this->fields[$name] : new StringType($name);

        return $field;
    }

    /**
     * Validate
     *
     * @param array $data data
     * 
     * @return bool
     */
    public function validate(array $data)
    {
        $isValid = true;
        foreach ($this->fields as $name => $field) {
            if (!$field->validate(array_key_exists($name, $data) ? $data[$name] : null)) {
                $isValid = false;
            }
        }

        return $isValid;
    }
}
