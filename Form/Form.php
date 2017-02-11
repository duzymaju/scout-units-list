<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\BasicType as Field;
use ScoutUnitsList\Model\ModelInterface;
use ScoutUnitsList\System\ParamPack;
use ScoutUnitsList\System\Request;
use ScoutUnitsList\System\View\Partial;
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
     * @param string         $viewPath view path
     * @param array          $settings settings
     */
    public function __construct(Request $request, ModelInterface $model, $viewPath, array $settings = [])
    {
        $this->setViewPath($viewPath);

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
     * Set action
     *
     * @param string $action action
     *
     * @return self
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Set model
     *
     * @param string $model model
     *
     * @return self
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
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
        $field = new $type($name, $this->getViewPath(), $settings);
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
     * @param array|null $fieldNames field names
     *
     * @return self
     */
    public function clear(array $fieldNames = null)
    {
        parent::clear();
        foreach ($this->fields as $field) {
            if (!is_array($fieldNames) || in_array($field->getName(), $fieldNames)) {
                $field->clear();
            }
        }

        return $this;
    }

    /**
     * Start rendering
     *
     * @param array  $params      params
     * @param string $partialName partial name
     */
    public function start(array $params = [], $partialName = 'Form/Start')
    {
        $partial = new Partial($this->getViewPath(), $partialName, [
            'action' => $this->action,
            'attr' => array_key_exists('attr', $params) ? $params['attr'] : [],
            'method' => $this->method,
        ]);
        $partial->render();
    }

    /**
     * Start rendering
     *
     * @param string      $name        name
     * @param string|null $partialName partial name
     */
    public function row($name, $partialName = null)
    {
        $field = $this->get($name);
        if (isset($field)) {
            if (empty($partialName)) {
                $field->row();
            } else {
                $field->row($partialName);
            }
        }
    }

    /**
     * Start rendering
     *
     * @param string      $name        name
     * @param string|null $partialName partial name
     */
    public function label($name, $partialName = null)
    {
        $field = $this->get($name);
        if (isset($field)) {
            if (empty($partialName)) {
                $field->label();
            } else {
                $field->label($partialName);
            }
        }
    }

    /**
     * Start rendering
     *
     * @param string|null $name name
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
     * @param string      $name        name
     * @param string|null $partialName partial name
     */
    public function widget($name, $partialName = null)
    {
        $field = $this->get($name);
        if (isset($field)) {
            if (empty($partialName)) {
                $field->widget();
            } else {
                $field->widget($partialName);
            }
        }
    }

    /**
     * End rendering
     *
     * @param string $partialName partial name
     */
    public function end($partialName = 'Form/End')
    {
        $partial = new Partial($this->getViewPath(), $partialName);
        $partial->render();
    }
}
