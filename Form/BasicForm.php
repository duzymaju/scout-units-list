<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\BasicType;
use ScoutUnitsList\System\Request;
use ScoutUnitsList\System\Tools\HelpersTrait;
use ScoutUnitsList\Model\ModelInterface;
use ScoutUnitsList\Validator\BasicValidator;

/**
 * Basic form
 */
abstract class BasicForm
{
    use HelpersTrait;

    /** @var string */
    protected $action;

    /** @var string */
    protected $method;

    /** @var array */
    protected $allowedMethods = array(
        Request::METHOD_POST,
        Request::METHOD_PUT,
    );

    /** @var Field[] */
    protected $fields = array();

    /** @var Request */
    protected $request;

    /** @var ModelInterface */
    protected $model;

    /** @var BasicValidator */
    protected $validator;

    /**
     * Constructor
     *
     * @param Request        $request  request
     * @param ModelInterface $model    model
     * @param array          $settings settings
     */
    public function __construct(Request $request, ModelInterface $model, array $settings = array())
    {
        $this->method = array_key_exists('method', $settings) ? strtolower($settings['method']) : Request::METHOD_POST;
        if (!in_array($this->method, $this->allowedMethods)) {
            $this->method = Request::METHOD_POST;
        }
        $this->action = array_key_exists('action', $settings) ? $settings['action'] : $request->getCurrentUrl();

        $this->request = $request;
        $this->model = $model;

        $this->setFields();

        $validatorClass = $this->getValidatorClass();
        $this->validator = new $validatorClass($this->fields);
    }

    /**
     * Set fields
     */
    abstract protected function setFields();

    /**
     * Add field
     *
     * @return self
     */
    protected function addField($name, $type, array $settings = array())
    {
        $field = new $type($name, $settings);
        if ($field instanceof BasicType) {
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
            $data = array();
            foreach ($this->fields as $name => $field) {
                $field->setValueFromParamPack($this->request->request);
            }

            $isValid = $this->validator->validate($data);

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
        foreach ($this->fields as $field) {
            $field->clear();
        }

        return $this;
    }

    /**
     * Start rendering
     *
     * @TODO: move to partial
     */
    public function start()
    {
        echo '<form action="' . $this->action . '" method="' . $this->escape($this->method) . '">';
    }

    /**
     * Start rendering
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
     * @TODO: move to partial
     */
    public function errors($name)
    {
        $field = $this->get($name);
        if (isset($field)) {
            $field->errors();
        }
    }

    /**
     * Start rendering
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
