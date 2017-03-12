<?php

namespace ScoutUnitsList\Model;

use ScoutUnitsList\System\Tools\DateTime;

/**
 * User model
 */
class User implements ModelInterface
{
    /** @const int */
    const PUBLISH_EMAIL_NO = 1;

    /** @const int */
    const PUBLISH_EMAIL_FORM = 2;

    /** @const int */
    const PUBLISH_EMAIL_YES = 3;

    /** @const string */
    const SEX_FEMALE = 'f';

    /** @const string */
    const SEX_MALE = 'm';

    /** @var int */
    protected $id;

    /** @var string */
    protected $login;

    /** @var string */
    protected $niceName;

    /** @var string */
    protected $email;

    /** @var int */
    protected $publishEmail;

    /** @var string */
    protected $grade;

    /** @var string */
    protected $duty;

    /** @var string */
    protected $responsibilities;

    /** @var string */
    protected $sex;

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
     * Get e-mail if allowed
     *
     * @return string|null
     */
    public function getEmailIfAllowed()
    {
        return $this->getPublishEmail() == self::PUBLISH_EMAIL_YES ? $this->email : null;
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
     * Get publish e-mail
     *
     * @return int
     */
    public function getPublishEmail()
    {
        return $this->publishEmail;
    }

    /**
     * Set publish e-mail
     *
     * @param int $publishEmail publish e-mail
     *
     * @return self
     */
    public function setPublishEmail($publishEmail)
    {
        $this->publishEmail = $publishEmail;

        return $this;
    }

    /**
     * Get grade
     *
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set grade
     *
     * @param string $grade grade
     *
     * @return self
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get duty
     *
     * @return string
     */
    public function getDuty()
    {
        return $this->duty;
    }

    /**
     * Set duty
     *
     * @param string $duty duty
     *
     * @return self
     */
    public function setDuty($duty)
    {
        $this->duty = $duty;

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
     * Get sex
     *
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set sex
     *
     * @param string $sex sex
     *
     * @return self
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Is female
     *
     * @return bool
     */
    public function isFemale()
    {
        $isFemale = $this->sex == self::SEX_FEMALE;

        return $isFemale;
    }

    /**
     * Is male
     *
     * @return bool
     */
    public function isMale()
    {
        $isMale = $this->sex == self::SEX_MALE;

        return $isMale;
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
     * @param string $url URL
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

    /**
     * Get name
     *
     * @param bool $addGrade add grade
     *
     * @return string
     */
    public function getName($addGrade = true)
    {
        $name = $this->getDisplayName();
        if ($addGrade && !empty($this->grade)) {
            $name = $this->grade . ' ' . $name;
        }

        return $name;
    }

    /**
     * Get avatar URL
     *
     * @param int|null    $size    size
     * @param string|null $default default
     *
     * @return string
     */
    public function getAvatarUrl($size = null, $default = null)
    {
        $arguments = [];
        if (!empty($size)) {
            $arguments['size'] = $size;
        }
        if (!empty($default)) {
            $arguments['default'] = $default;
        }
        $url = \get_avatar_url($this->getEmail(), $arguments);

        return $url;
    }
}
