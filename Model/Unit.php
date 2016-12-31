<?php

namespace ScoutUnitsList\Model;

use ScoutUnitsList\System\Tools\StringTrait;

/**
 * Unit model
 */
class Unit implements ModelInterface
{
    use StringTrait;

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

    /** @var string|null */
    protected $subtype;

    /** @var int */
    protected $sort = 0;

    /** @var int|null */
    protected $parentId;

    /** @var self|null */
    protected $parent;

    /** @var string */
    protected $orderNo;

    /** @var string */
    protected $slug;

    /** @var string */
    protected $name;

    /** @var string|null */
    protected $nameFull;

    /** @var string|null */
    protected $hero;

    /** @var string|null */
    protected $heroFull;

    /** @var string|null */
    protected $url;

    /** @var string|null */
    protected $mail;

    /** @var string|null */
    protected $address;

    /** @var string|null */
    protected $meetingsTime;

    /** @var float|null */
    protected $localizationLat;

    /** @var float|null */
    protected $localizationLng;

    /** @var array */
    protected $persons = [];

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
     * Is active
     *
     * @return bool
     */
    public function isActive()
    {
        $isActive = $this->status == self::STATUS_ACTIVE;

        return $isActive;
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
     * @return string|null
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * Set subtype
     *
     * @param string|null $subtype subtype
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
     * @return int|null
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set parent ID
     *
     * @param int|null $parentId parent ID
     *
     * @return self
     */
    public function setParentId($parentId)
    {
        $this->parentId = (int) $parentId;
        if ($this->parentId < 1) {
            $this->parentId = null;
        }

        return $this;
    }

    /**
     * Get parent
     *
     * @return self|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @param self $parent parent
     *
     * @return self
     */
    public function setParent(self $parent)
    {
        $this->parent = $parent;

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
        $this->slug = $this->convertToSlug($slug);

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

        if (empty($this->slug)) {
            $this->setSlug($this->name);
        }

        return $this;
    }

    /**
     * Get full name
     *
     * @return string|null
     */
    public function getNameFull()
    {
        return $this->nameFull;
    }

    /**
     * Set full name
     *
     * @param string|null $nameFull full name
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
     * @return string|null
     */
    public function getHero()
    {
        return $this->hero;
    }

    /**
     * Set hero
     *
     * @param string|null $hero hero
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
     * @return string|null
     */
    public function getHeroFull()
    {
        return $this->heroFull;
    }

    /**
     * Set full hero
     *
     * @param string|null $heroFull full hero
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
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set URL
     *
     * @param string|null $url URL
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
     * @return string|null
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set mail
     *
     * @param string|null $mail mail
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
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param string|null $address address
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
     * @return string|null
     */
    public function getMeetingsTime()
    {
        return $this->meetingsTime;
    }

    /**
     * Set meetings time
     *
     * @param string|null $meetingsTime meetings time
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
     * @return float|null
     */
    public function getLocalizationLat()
    {
        return $this->localizationLat;
    }

    /**
     * Set localization latitude
     *
     * @param float|null $localizationLat localization latitude
     *
     * @return self
     */
    public function setLocalizationLat($localizationLat)
    {
        $this->localizationLat = isset($localizationLat) ? (float) $localizationLat : null;

        return $this;
    }

    /**
     * Get localization longitude
     *
     * @return float|null
     */
    public function getLocalizationLng()
    {
        return $this->localizationLng;
    }

    /**
     * Set localization longitude
     *
     * @param float|null $localizationLng localization longitude
     *
     * @return self
     */
    public function setLocalizationLng($localizationLng)
    {
        $this->localizationLng = isset($localizationLng) ? (float) $localizationLng : null;

        return $this;
    }

    /**
     * Add person
     *
     * @param Person $person person
     *
     * @return self
     */
    public function addPerson(Person $person)
    {
        foreach ($this->persons as $currentPerson) {
            if ($person->getId() == $currentPerson->getId()) {
                return $this;
            }
        }
        $this->persons[] = $person;

        return $this;
    }

    /**
     * Get persons
     *
     * @return Person[]
     */
    public function getPersons()
    {
        return $this->persons;
    }

    /**
     * Set persons
     *
     * @param Person[] $persons persons
     *
     * @return self
     */
    public function setPersons(array $persons)
    {
        $this->persons = $persons;

        return $this;
    }
}
