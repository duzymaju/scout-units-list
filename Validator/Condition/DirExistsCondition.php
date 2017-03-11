<?php

namespace ScoutUnitsList\Validator\Condition;

/**
 * Validator dir exists condition
 */
class DirExistsCondition implements ConditionInterface
{
    /** @var string */
    private $baseDir;

    /** @var bool */
    private $ignoreIfEmpty;

    /**
     * Constructor
     *
     * @param string $baseDir       base dir
     * @param bool   $ignoreIfEmpty ignore if empty
     */
    public function __construct($baseDir, $ignoreIfEmpty = false)
    {
        $this->baseDir = $baseDir;
        $this->ignoreIfEmpty = $ignoreIfEmpty;
    }

    /**
     * Check
     *
     * @param mixed $value value
     *
     * @return array
     */
    public function check($value)
    {
        $errors = [];

        if (!$this->ignoreIfEmpty || !empty($value)) {
            $baseDirSlash = preg_match('#/$#', $this->baseDir);
            $valueBeginSlash = preg_match('#^/#', $value);
            $valueEndSlash = preg_match('#/$#', $value);

            if ($valueEndSlash && $value != '/') {
                $errors[] = __('Path shouldn\'t end on slash.', 'scout-units-list');
            } elseif ($baseDirSlash && $valueBeginSlash) {
                $errors[] = __('Path shouldn\'t begin from slash.', 'scout-units-list');
            } elseif (!$baseDirSlash && !$valueBeginSlash) {
                $errors[] = __('Path should begin from slash.', 'scout-units-list');
            } elseif (!is_dir($this->baseDir . $value)) {
                $errors[] = __('Selected directory doesn\'t exist.', 'scout-units-list');
            }
        }

        return $errors;
    }
}
