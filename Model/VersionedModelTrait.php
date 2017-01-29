<?php

namespace ScoutUnitsList\Model;

use ScoutUnitsList\System\Tools\DateTime;

/**
 * Versioned model trait
 */
trait VersionedModelTrait
{
    /** @var int */
    protected $groupId = 0;

    /** @var int */
    protected $action = VersionedModelInterface::ACTION_INSERTED;

    /** @var DateTime */
    protected $createdAt;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Get group ID
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set group ID
     *
     * @param int $groupId group ID
     *
     * @return self
     */
    public function setGroupId($groupId)
    {
        $this->groupId = (int) $groupId;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
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
     * Get created at
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set created at
     *
     * @param DateTime|string $createdAt created at
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt instanceof DateTime ? $createdAt : new DateTime($createdAt);

        return $this;
    }
}
