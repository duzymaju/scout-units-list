<?php

namespace ScoutUnitsList\Manager;

use Exception;
use ScoutUnitsList\Exception\NoTypeException;

/**
 * Types manager
 */
class TypesManager
{
    /** @var string */
    const LANGUAGE_DEFAULT = 'en';

    /** @var string */
    private $dirPath;

    /** @var string */
    private $language;

    /** @var array|null */
    private $structure;

    /**
     * Constructor
     *
     * @param string       $dirPath directory path
     * @param string       $locale  locale
     * @param string|array $typeSet type set
     *
     * @throws NoTypeException
     */
    public function __construct($dirPath, $locale, $typeSet)
    {
        $this->dirPath = $dirPath;
        $this->language = substr($locale, 0, 2);
        $this->loadTypeSet($typeSet);
    }

    /**
     * Load type set
     *
     * @param string|array $typeSet type set
     *
     * @return self
     *
     * @throws NoTypeException
     */
    public function loadTypeSet($typeSet)
    {
        if (is_string($typeSet)) {
            $this->structure = $this->getStructure($typeSet);
        } elseif (is_array($typeSet)) {
            $this->structure = $this->validate($typeSet) ? $typeSet : null;
        }
        if (!isset($this->structure)) {
            throw new NoTypeException('Types aren\'t defined.');
        }

        return $this;
    }

    /**
     * Get list
     *
     * @return array
     */
    public function getList()
    {
        $list = [];
        foreach (scandir($this->dirPath) as $fileName) {
            $structure = $this->getStructure($fileName);
            if (is_array($structure)) {
                $list[$fileName] = $this->getName($structure['name']) .
                    ' (' . strtoupper($structure['nationality']) . ')';
            }
        }

        return $list;
    }

    /**
     * Get types
     *
     * @return array
     */
    public function getTypes()
    {
        $types = [];
        foreach ($this->structure['types'] as $key => $type) {
            $types[$key] = $this->getName($type['name']);
        }
        return $types;
    }

    /**
     * Get type name
     *
     * @param string $type type
     *
     * @return string|null
     */
    public function getTypeName($type)
    {
        $typeName = array_key_exists($type, $this->structure['types']) ?
            $this->getName($this->structure['types'][$type]['name']) : null;

        return $typeName;
    }

    /**
     * Get subtypes
     *
     * @return array
     */
    public function getSubtypes()
    {
        $subtypes = [];
        foreach ($this->structure['subtypes'] as $key => $subtype) {
            $subtypes[$key] = $this->getName($subtype['name']);
        }
        foreach ($subtypes as $key => $name) {
            $subtypes[$key] = [
                'attr' => [
                    'data-for-type' => $this->getTypeForSubtype($key),
                ],
                'name' => $name,
            ];
        }

        return $subtypes;
    }

    /**
     * Get possible parent types
     *
     * @param string $childType child type
     *
     * @return array
     */
    public function getPossibleParentTypes($childType)
    {
        if (!array_key_exists($childType, $this->structure['types']) ||
            !is_array($this->structure['types'][$childType]['possibleParents'])
        ) {
            return [];
        }

        return $this->structure['types'][$childType]['possibleParents'];
    }

    /**
     * Get type for subtype
     *
     * @param string $subtype subtype
     *
     * @return string|null
     */
    public function getTypeForSubtype($subtype)
    {
        foreach ($this->structure['types'] as $key => $type) {
            if (is_array($type['subtypes']) && in_array($subtype, $type['subtypes'])) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Get structure
     *
     * @param string $fileName file name
     *
     * @return array|null
     */
    private function getStructure($fileName)
    {
        if (substr($fileName, -4) !== 'json') {
            return null;
        }
        $filePath = $this->dirPath . '/' . $fileName;
        if (!is_file($filePath)) {
            return null;
        }
        $json = file_get_contents($filePath);
        if ($json === false) {
            return null;
        }
        $structure = json_decode($json, true);

        return $structure;
    }

    /**
     * Get name
     *
     * @param array $names names
     *
     * @return string|null
     */
    private function getName(array $names)
    {
        if (array_key_exists($this->language, $names)) {
            return $names[$this->language];
        }
        if (array_key_exists(self::LANGUAGE_DEFAULT, $names)) {
            return $names[self::LANGUAGE_DEFAULT];
        }

        return null;
    }

    /**
     * Validate
     *
     * @param array $pack pack
     *
     * @return bool
     */
    public function validate($pack)
    {
        try {
            if (!is_array($pack)) {
                throw new Exception('Type pack should be an array.');
            }
            if (empty($pack['nationality']) || !is_string($pack['nationality'])) {
                throw new Exception('Element `nationality` has to be defined.');
            }
            $this->validateName(array_key_exists('name', $pack) ? $pack['name'] : null);
            $this->validateItems('types', array_key_exists('types', $pack) ? $pack['types'] : null);
            if (array_key_exists('subtypes', $pack)) {
                $this->validateItems('subtypes', $pack['subtypes']);
            }
            $this->validateRelations($pack['types'], 'possibleParents', array_keys($pack['types']));
            $this->validateRelations($pack['types'], 'subtypes', array_keys($pack['subtypes']));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Validate items
     *
     * @param string $category category
     * @param array  $items    items
     *
     * @throws Exception
     */
    private function validateItems($category, $items)
    {
        if (!is_array($items)) {
            throw new Exception(sprintf('List of %s items should be an array.', $category));
        }
        if (count($items) === 0) {
            throw new Exception(sprintf('List of %s items shouldn\'t be empty.', $category));
        }
        foreach ($items as $key => $item) {
            if (!preg_match('#^[a-z]$#', $key)) {
                throw new Exception(sprintf('Key in a list of %s items should be lower case letter.', $category));
            }
            try {
                $this->validateItem($item);
            } catch (Exception $e) {
                throw new Exception(sprintf('Item %s of %s: %s', $key, $category, $e->getMessage()), 0, $e);
            }
        }
    }

    /**
     * Validate item
     *
     * @param array $item item
     *
     * @throws Exception
     */
    private function validateItem($item)
    {
        if (!is_array($item)) {
            throw new Exception('Type pack\'s item should be an array.');
        }
        $this->validateName($item['name']);
        if (array_key_exists('possibleParents', $item)) {
            $this->validateReferences($item['possibleParents']);
        }
        if (array_key_exists('subtypes', $item)) {
            $this->validateReferences($item['subtypes']);
        }
    }

    /**
     * Validate name
     *
     * @param array $names names
     *
     * @throws Exception
     */
    private function validateName($names)
    {
        if (!is_array($names)) {
            throw new Exception('Type pack\'s name should be an array.');
        }
        foreach ($names as $key => $name) {
            if (!preg_match('#^[a-z]{2}$#', $key)) {
                throw new Exception('Language code should be two lower case letters.');
            }
            if (!is_string($name) || empty($name)) {
                throw new Exception('Name shouldn\'t be empty.');
            }
        }
        if (!array_key_exists(self::LANGUAGE_DEFAULT, $names)) {
            throw new Exception(sprintf('There should be a name in default language (%s).', self::LANGUAGE_DEFAULT));
        }
    }

    /**
     * Validate references
     *
     * @param array $list list
     *
     * @throws Exception
     */
    private function validateReferences(array $list)
    {
        if (!is_array($list)) {
            throw new Exception('Type pack\'s references list should be an array.');
        }
        foreach ($list as $item) {
            if (!is_string($item)) {
                throw new Exception('Type pack\'s reference should be a string.');
            }
        }
    }

    /**
     * Validate relations
     *
     * @param array  $types     types
     * @param string $fieldName field name
     * @param array  $allKeys   all keys
     *
     * @throws Exception
     */
    private function validateRelations(array $types, $fieldName, array $allKeys)
    {
        foreach ($types as $typeKey => $type) {
            if (array_key_exists($fieldName, $type)) {
                $unexpectedKeys = array_diff($type[$fieldName], $allKeys);
                if (count($unexpectedKeys) > 0) {
                    throw new Exception(sprintf('There are unexpected keys ("%s") for %s field of type %s.',
                        implode('", "', $unexpectedKeys), $fieldName, $typeKey));
                }
            }
        }
    }
}
