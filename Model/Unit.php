<?php

namespace ScoutUnitsList\Model;

/**
 * Unit model
 */
class Unit
{
    /** @const string */
    const STATUS_ACTIVE = 'a';

    /** @const string */
    const STATUS_HIDDEN = 'h';

    /** @const string */
    const TYPE_GROUP = 'g';

    /** @const string */
    const TYPE_TROOP = 't';

    /** @const string */
    const TYPE_PATROL = 'p';

    /** @const string */
    const TYPE_CLUB = 'c';

    /** @const string */
    const SUBTYPE_CUBSCOUTS = 'c';

    /** @const string */
    const SUBTYPE_SCOUTS = 's';

    /** @const string */
    const SUBTYPE_SENIORS_COUTS = 'e';

    /** @const string */
    const SUBTYPE_ROVERS = 'r';

    /** @const string */
    const SUBTYPE_MULTI_LEVEL = 'm';

    /** @const string */
    const SUBTYPE_GROUP = 'g';

    /** @const string */
    const SUBTYPE_UNION_OF_GROUPS = 'u';

    /** @var int */
    protected $id;

    /** @var string */
    protected $status;

    /** @var string */
    protected $type;

    /** @var string */
    protected $subtype;

    /** @var int */
    protected $sort;

    /** @var int */
    protected $parentId;

    /** @var string */
    protected $slug;

    /** @var string */
    protected $name;

    /** @var string */
    protected $nameFull;

    /** @var string */
    protected $hero;

    /** @var string */
    protected $heroFull;

    /** @var string */
    protected $url;

    /** @var string */
    protected $mail;

    /** @var string */
    protected $address;

    /** @var string */
    protected $meetingsTime;

    /** @var float */
    protected $localizationLat;

    /** @var float */
    protected $localizationLng;

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
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
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
     * Get subtype
     *
     * @return string
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * Set subtype
     *
     * @param string $subtype subtype
     *
     * @return self
     */
    public function setSubtype($subtype)
    {
        $this->subtype = $subtype;

        return $this;
    }

    /**
     * Get sort
     *
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set sort
     *
     * @param int $sort sort
     *
     * @return self
     */
    public function setSort($sort)
    {
        $this->sort = (int) $sort;

        return $this;
    }

    /**
     * Get parent ID
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set parent ID
     *
     * @param int $parentId parent ID
     *
     * @return self
     */
    public function setParentId($parentId)
    {
        $this->parentId = (int) $parentId;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string $slug slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getNameFull()
    {
        return $this->nameFull;
    }

    /**
     * Set full name
     *
     * @param string $nameFull full name
     *
     * @return self
     */
    public function setNameFull($nameFull)
    {
        $this->nameFull = $nameFull;

        return $this;
    }

    /**
     * Get hero
     *
     * @return string
     */
    public function getHero()
    {
        return $this->hero;
    }

    /**
     * Set hero
     *
     * @param string $hero hero
     *
     * @return self
     */
    public function setHero($hero)
    {
        $this->hero = $hero;

        return $this;
    }

    /**
     * Get full hero
     *
     * @return string
     */
    public function getHeroFull()
    {
        return $this->heroFull;
    }

    /**
     * Set full hero
     *
     * @param string $heroFull full hero
     *
     * @return self
     */
    public function setHeroFull($heroFull)
    {
        $this->heroFull = $heroFull;

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
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set mail
     *
     * @param string $mail mail
     *
     * @return self
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->status;
    }

    /**
     * Set address
     *
     * @param string $address address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get meetings time
     *
     * @return string
     */
    public function getMeetingsTime()
    {
        return $this->meetingsTime;
    }

    /**
     * Set meetings time
     *
     * @param string $meetingsTime meetings time
     *
     * @return self
     */
    public function setMeetingsTime($meetingsTime)
    {
        $this->meetingsTime = $meetingsTime;

        return $this;
    }

    /**
     * Get localization latitude
     *
     * @return float
     */
    public function getLocalizationLat()
    {
        return $this->localizationLat;
    }

    /**
     * Set localization latitude
     *
     * @param float $localizationLat localization latitude
     *
     * @return self
     */
    public function setLocalizationLat($localizationLat)
    {
        $this->localizationLat = (float) $localizationLat;

        return $this;
    }

    /**
     * Get localization longitude
     *
     * @return float
     */
    public function getLocalizationLng()
    {
        return $this->status;
    }

    /**
     * Set localization longitude
     *
     * @param float $localizationLng localization longitude
     *
     * @return self
     */
    public function setLocalizationLng($localizationLng)
    {
        $this->localizationLng = (float) $localizationLng;

        return $this;
    }
}
