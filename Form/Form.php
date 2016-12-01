<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\BasicType as Field;
use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\System\Request;
use ScoutUnitsList\Model\ModelInterface;
use ScoutUnitsList\Validator\Validator;

/**
 * Form
 */
abstract class Form extends FormElement
{
    /** @var string */
    protected $action;

    /** @var string */
    protected $method;

    /** @var array */
    protected $allowedMethods = [
        Request::METHOD_POST,
        Request::METHOD_PUT,
    ];

    /** @var Field[] */
    protected $fields = [];

    /** @var Request */
    protected $request;

    /** @var ModelInterface */
    protected $model;

    /** @var Validator */
    protected $validator;

    /**
     * Constructor
     *
     * @param Request        $request  request
     * @param ModelInterface $model    model
     * @param array          $settings settings
     */
    public function __construct(Request $request, ModelInterface $model, array $settings = [])
    {
        $this->method = array_key_exists('method', $settings) ? strtolower($settings['method']) : Request::METHOD_POST;
        if (!in_array($this->method, $this->allowedMethods)) {
            $this->method = Request::METHOD_POST;
        }
        $this->action = array_key_exists('action', $settings) ? $settings['action'] : $request->getCurrentUrl();

        $this->setValue($request->request);
        $this->request = $request;
        $this->model = $model;

        $this->setFields($settings);

        $validatorClass = $this->getValidatorClass();
        $this->validator = new $validatorClass($this, array_key_exists('validator', $settings) ?
            $settings['validator'] : []);
    }

    /**
     * Get value
     *
     * @return ParamPack
     */
    public function getValue()
    {
        return parent::getValue();
    }

    /**
     * Set value
     *
     * @param ParamPack $value value
     *
     * @return self
     */
    public function setValue(ParamPack $value)
    {
        return parent::setValue($value);
    }

    /**
     * Get model
     *
     * @return ModelInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get fields
     *
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set fields
     *
     * @param array $settings settings
     */
    abstract protected function setFields(array $settings);

    /**
     * Add field
     *
     * @return self
     */
    protected function addField($name, $type, array $settings = [])
    {
        $field = new $type($name, $settings);
        if ($field instanceof Field) {
            $this->fields[$name] = $field;
        }

        return $this;
    }

    /**
     * Get validator class
     *
     * @return string
     */
    abstract protected function getValidatorClass();

    /**
     * Get
     *
     * @param string $name name
     *
     * @return Field|null
     */
    public function get($name)
    {
        $field = array_key_exists($name, $this->fields) ? $this->fields[$name] : null;

        return $field;
    }

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        $isValid = false;

        if (($this->method == Request::METHOD_POST && $this->request->isPost()) ||
            ($this->method == Request::METHOD_PUT && $this->request->isPut()))
        {
            foreach ($this->fields as $name => $field) {
                $field->setValueFromParamPack($this->getValue());
            }

            $isValid = $this->validator->validate();

            if ($isValid) {
                foreach ($this->fields as $name => $field) {
                    $method = 'set' . ucfirst($name);
                    if (method_exists($this->model, $method)) {
                        $this->model->$method($field->getValue());
                    }
                }
            }
        } else {
            foreach ($this->fields as $name => $field) {
                $ucName = ucfirst($name);
                $methodGet = 'get' . $ucName;
                $methodIs = 'is' . $ucName;
                $methodHas = 'has' . $ucName;
                if (method_exists($this->model, $methodGet)) {
                    $field->setValue($this->model->$methodGet());
                } elseif (method_exists($this->model, $methodIs)) {
                    $field->setValue($this->model->$methodIs());
                } elseif (method_exists($this->model, $methodHas)) {
                    $field->setValue($this->model->$methodHas());
                }
            }
        }

        return $isValid;
    }

    /**
     * Clear
     *
     * @return self
     */
    public function clear()
    {
        parent::clear();
        foreach ($this->fields as $field) {
            $field->clear();
        }

        return $this;
    }

    /**
     * Start rendering
     *
     * @param array $params params
     *
     * @TODO: move to partial
     */
    public function start(array $params = [])
    {
        $attr = '';
        if (array_key_exists('attr', $params)) {
            foreach ($params['attr'] as $key => $value) {
                $attr .= ' ' . $this->escape($key) . '="' . $this->escape($value) . '"';
            }
        }
        echo '<form action="' . $this->action . '" method="' . $this->escape($this->method) . '"' . $attr . '>';
    }

    /**
     * Start rendering
     *
     * @param string $name name
     *
     * @TODO: move to partial
     */
    public function row($name)
    {
        $field = $this->get($name);
        if (isset($field)) {
            $field->row();
        }
    }

    /**
     * Start rendering
     *
     * @param string $name name
     *
     * @TODO: move to partial
     */
    public function label($name)
    {
        $field = $this->get($name);
        if (isset($field)) {
            $field->label();
        }
    }

    /**
     * Start rendering
     *
     * @param string|null $name name
     *
     * @TODO: move to partial
     */
    public function errors($name = null)
    {
        if (isset($name)) {
            $field = $this->get($name);
            if (isset($field)) {
                $field->errors();
            }
        } else {
            parent::errors();
        }
    }

    /**
     * Start rendering
     *
     * @param string $name name
     *
     * @TODO: move to partial
     */
    public function widget($name)
    {
        $field = $this->get($name);
        if (isset($field)) {
            $field->widget();
        }
    }

    /**
     * End rendering
     *
     * @TODO: move to partial
     */
    public function end()
    {
        echo '</form>';
    }
}
