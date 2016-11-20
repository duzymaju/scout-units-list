<?php

namespace ScoutUnitsList\Model;

/**
 * Person model
 */
class Person implements ModelInterface
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $userId;

    /** @var int */
    protected $unitId;

    /** @var int */
    protected $positionId;

    /** @var string */
    protected $orderNo;

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
     * Get user ID
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set user ID
     *
     * @param int $userId user ID
     *
     * @return self
     */
    public function setUserId($userId)
    {
        $this->userId = (int) $userId;

        return $this;
    }

    /**
     * Get unit ID
     *
     * @return int
     */
    public function getUnitId()
    {
        return $this->unitId;
    }

    /**
     * Set unit ID
     *
     * @param int $unitId unit ID
     *
     * @return self
     */
    public function setUnitId($unitId)
    {
        $this->unitId = (int) $unitId;

        return $this;
    }

    /**
     * Get position ID
     *
     * @return int
     */
    public function getPositionId()
    {
        return $this->positionId;
    }

    /**
     * Set position ID
     *
     * @param int $positionId position ID
     *
     * @return self
     */
    public function setPositionId($positionId)
    {
        $this->positionId = (int) $positionId;

        return $this;
    }

    /**
     * Get order no
     *
     * @return string
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * Set order no
     *
     * @param string $orderNo order no
     *
     * @return self
     */
    public function setOrderNo($orderNo)
    {
        $this->orderNo = $orderNo;

        return $this;
    }
}
