<?php

namespace ScoutUnitsList\Form;

use ScoutUnitsList\Form\Field\IntegerType;
use ScoutUnitsList\Form\Field\SelectType;
use ScoutUnitsList\Form\Field\StringType;
use ScoutUnitsList\Form\Field\SubmitType;
use ScoutUnitsList\Model\ModelInterface;
use ScoutUnitsList\System\Request;
use ScoutUnitsList\Validator\PersonValidator;

/**
 * Person form
 */
class PersonForm extends BasicForm
{
    /** @var array */
    private $positions;

    /**
     * Constructor
     *
     * @param Request        $request   request
     * @param ModelInterface $model     model
     * @param array          $positions positions
     * @param array          $settings  settings
     */
    public function __construct(Request $request, ModelInterface $model, array $positions, array $settings = [])
    {
        $this->positions = $positions;
        parent::__construct($request, $model, $settings);
    }

    /**
     * Set fields
     */
    protected function setFields()
    {
        $this
            ->addField('userId', IntegerType::class, array(
                'label' => __('User ID', 'wpcore'),
                'required' => true,
            ))
            ->addField('positionId', SelectType::class, array(
                'label' => __('Position', 'wpcore'),
                'options' => $this->positions,
                'required' => true,
            ))
            ->addField('orderNo', StringType::class, array(
                'label' => __('Order number', 'wpcore'),
                'required' => true,
            ))
            ->addField('submit', SubmitType::class, array(
                'label' => __('Save', 'wpcore'),
            ))
        ;
    }

    /**
     * Get validator class
     *
     * @return string
     */
    protected function getValidatorClass()
    {
        return PersonValidator::class;
    }
}
