<?php

namespace ScoutUnitsList\Model;

use ScoutUnitsList\System\Tools\DateTime;

/**
 * Versioned model interface
 */
interface VersionedModelInterface extends ModelInterface
{
    /** @var string */
    const ACTION_INSERTED = 'i';

    /** @var string */
    const ACTION_MODIFIED = 'm';

    /** @var string */
    const ACTION_REMOVED = 'r';

    /**
     * Get group ID
     *
     * @return int
     */
    public function getGroupId();

    /**
     * Set group ID
     *
     * @param int $groupId group ID
     *
     * @return self
     */
    public function setGroupId($groupId);

    /**
     * Get action
     *
     * @return string
     */
    public function getAction();

    /**
     * Set action
     *
     * @param string $action action
     *
     * @return self
     */
    public function setAction($action);

    /**
     * Get created at
     *
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param DateTime|string $createdAt created at
     *
     * @return self
     */
    public function setCreatedAt($createdAt);
}
