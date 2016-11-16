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
    protected $positionId;

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
    public function setStatus($positionId)
    {
        $this->positionId = (int) $positionId;

        return $this;
    }
}
