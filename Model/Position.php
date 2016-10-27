<?php

namespace ScoutUnitsList\Model;

/**
 * Position model
 */
class Position
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $nameMale;

    /** @var string */
    protected $nameFemale;

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
