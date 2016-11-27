<?php

namespace ScoutUnitsList\Model;

use DateTime;

/**
 * User model
 */
class User implements ModelInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $login;

    /** @var string */
    protected $niceName;

    /** @var string */
    protected $email;

    /** @var string */
    protected $url;

    /** @var DateTime */
    protected $registered;

    /** @var int */
    protected $status;

    /** @var string */
    protected $displayName;

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
     * Set ID
     *
     * @param int $id ID
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set login
     *
     * @param string $login login
     *
     * @return self
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get nice name
     *
     * @return string
     */
    public function getNiceName()
    {
        return $this->niceName;
    }

    /**
     * Set nice name
     *
     * @param string $niceName nice name
     *
     * @return self
     */
    public function setNiceName($niceName)
    {
        $this->niceName = $niceName;

        return $this;
    }

    /**
     * Get e-mail
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set e-mail
     *
     * @param string $email e-mail
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set URL
     *
     * @param DateTime|string $url URL
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get registered
     *
     * @return DateTime
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Set registered
     *
     * @param DateTime|string $registered registered
     *
     * @return self
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered instanceof DateTime ? $registered : new DateTime($registered);

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param int $status status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get display name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set display name
     *
     * @param string $displayName display name
     *
     * @return self
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }
}
