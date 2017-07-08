<?php

namespace ScoutUnitsList\Model;

/**
 * Position model
 */
class Position implements ModelInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $type;

    /** @var string */
    protected $nameMale;

    /** @var string */
    protected $nameFemale;

    /** @var string */
    protected $description;

    /** @var string */
    protected $responsibilities;

    /** @var bool */
    protected $leader;

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get name male
     *
     * @return string
     */
    public function getNameMale()
    {
        return $this->nameMale;
    }

    /**
     * Set name male
     *
     * @param string $nameMale name male
     *
     * @return self
     */
    public function setNameMale($nameMale)
    {
        $this->nameMale = $nameMale;

        return $this;
    }

    /**
     * Get name female
     *
     * @return string
     */
    public function getNameFemale()
    {
        return $this->nameFemale;
    }

    /**
     * Set name female
     *
     * @param string $nameFemale name female
     *
     * @return self
     */
    public function setNameFemale($nameFemale)
    {
        $this->nameFemale = $nameFemale;

        return $this;
    }

    /**
     * Get name for
     *
     * @param User|null $user user
     *
     * @return string
     */
    public function getNameFor(User $user = null)
    {
        $name = isset($user) && $user->isFemale() ? $this->getNameFemale() : $this->getNameMale();

        return $name;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get responsibilities
     *
     * @return string
     */
    public function getResponsibilities()
    {
        return $this->responsibilities;
    }

    /**
     * Set responsibilities
     *
     * @param string $responsibilities responsibilities
     *
     * @return self
     */
    public function setResponsibilities($responsibilities)
    {
        $this->responsibilities = $responsibilities;

        return $this;
    }

    /**
     * Is leader
     *
     * @return bool
     */
    public function isLeader()
    {
        return $this->leader;
    }

    /**
     * Set leader
     *
     * @param bool $leader leader
     *
     * @return self
     */
    public function setLeader($leader)
    {
        $this->leader = (bool) $leader;

        return $this;
    }
}
