<?php

namespace ScoutUnitsList\Model;

use JsonSerializable;
use ScoutUnitsList\System\Tools\StringTrait;

/**
 * Unit model
 */
class Unit implements JsonSerializable, VersionedModelInterface
{
    use StringTrait;
    use VersionedModelTrait;

    /** @const string */
    const TYPE_CLUB = 'c';

    /** @const string */
    const TYPE_DISTRICT = 'd';

    /** @const string */
    const TYPE_GROUP = 'g';

    /** @const string */
    const TYPE_HQ = 'h';

    /** @const string */
    const TYPE_PATROL = 'p';

    /** @const string */
    const TYPE_REGION = 'r';

    /** @const string */
    const TYPE_SECTION = 's';

    /** @const string */
    const TYPE_TROOP = 't';

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
    protected $type;

    /** @var string|null */
    protected $subtype;

    /** @var int */
    protected $sort = 0;

    /** @var int|null */
    protected $parentId;

    /** @var self|null */
    protected $parent;

    /** @var int|null */
    protected $orderId;

    /** @var Attachment|null */
    protected $order;

    /** @var string */
    protected $orderNo;

    /** @var string|null */
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
    protected $children = [];

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
     * Get order ID
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set order ID
     *
     * @param int|null $orderId order ID
     *
     * @return self
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get order
     *
     * @return Attachment|null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set order
     *
     * @param Attachment $order order
     *
     * @return self
     */
    public function setOrder(Attachment $order)
    {
        $this->order = $order;

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
     * @return string|null
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string|null $slug slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = empty($slug) ? null : $this->convertToSlug($slug);

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
     * Get completeness ratio
     *
     * @return float
     */
    public function getCompletenessRatio()
    {
        $components = [
            !empty($this->getUrl()),
            !empty($this->getMail()),
            !empty($this->getAddress()),
            !empty($this->getMeetingsTime()),
            !empty($this->getLocalizationLat()) && !empty($this->getLocalizationLng()),
        ];

        $completeCounter = 0;
        foreach ($components as $component) {
            if ($component) {
                $completeCounter++;
            }
        }
        $ratio = $completeCounter / count($components);

        return $ratio;
    }

    /**
     * Add child
     *
     * @param Unit $child child
     *
     * @return self
     */
    public function addChild(Unit $child)
    {
        foreach ($this->children as $currentChild) {
            if ($child->getId() == $currentChild->getId()) {
                return $this;
            }
        }
        $this->children[] = $child;

        return $this;
    }

    /**
     * Get children
     *
     * @return Unit[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set children
     *
     * @param Unit[] $children children
     *
     * @return self
     */
    public function setChildren(array $children)
    {
        $this->children = $children;

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

    /**
     * JSON serialize
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $children = [];
        foreach ($this->children as $child) {
            $children[] = $child->jsonSerialize();
        }

        $persons = [];
        foreach ($this->persons as $person) {
            $persons[] = $person->jsonSerialize();
        }

        $data = [
            'address' => $this->address,
            'children' => $children,
            'hero' => $this->hero,
            'heroFull' => $this->heroFull,
            'localization' => !empty($this->localizationLat) && !empty($this->localizationLng) ? [
                'lat' => $this->localizationLat,
                'lng' => $this->localizationLng,
            ] : null,
            'mail' => $this->mail,
            'meetingsTime' => $this->meetingsTime,
            'name' => $this->name,
            'nameFull' => $this->nameFull,
            'parent' => isset($this->parent) ? $this->parent->getSlug() : null,
            'persons' => $persons,
            'slug' => $this->slug,
            'subtype' => $this->subtype,
            'type' => $this->type,
            'url' => $this->url,
        ];

        return $data;
    }
}
