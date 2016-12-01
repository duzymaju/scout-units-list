<?php

namespace ScoutUnitsList\Manager;

/**
 * Message manager
 */
class MessageManager
{
    /** @const string */
    const TYPE_ERROR = 'error';

    /** @const string */
    const TYPE_INFO = 'info';

    /** @const string */
    const TYPE_SUCCESS = 'success';

    /** @const string */
    const TYPE_WARNING = 'warning';

    /** @var array */
    protected $messages = [];

    /**
     * Add info
     *
     * @param string $text        text
     * @param array  $cssClasses  CSS classes
     * @param bool   $dismissible dismissible
     *
     * @return self
     */
    public function addInfo($text, array $cssClasses = [], $dismissible = true)
    {
        return $this->addMessage($text, self::TYPE_INFO, $cssClasses, $dismissible);
    }

    /**
     * Add success
     *
     * @param string $text        text
     * @param array  $cssClasses  CSS classes
     * @param bool   $dismissible dismissible
     *
     * @return self
     */
    public function addSuccess($text, array $cssClasses = [], $dismissible = true)
    {
        return $this->addMessage($text, self::TYPE_SUCCESS, $cssClasses, $dismissible);
    }

    /**
     * Add warning
     *
     * @param string $text        text
     * @param array  $cssClasses  CSS classes
     * @param bool   $dismissible dismissible
     *
     * @return self
     */
    public function addWarning($text, array $cssClasses = [], $dismissible = true)
    {
        return $this->addMessage($text, self::TYPE_WARNING, $cssClasses, $dismissible);
    }

    /**
     * Add error
     *
     * @param string $text        text
     * @param array  $cssClasses  CSS classes
     * @param bool   $dismissible dismissible
     *
     * @return self
     */
    public function addError($text, array $cssClasses = [], $dismissible = true)
    {
        return $this->addMessage($text, self::TYPE_ERROR, $cssClasses, $dismissible);
    }

    /**
     * Add message
     *
     * @param string $text        text
     * @param string $type        type
     * @param array  $cssClasses  CSS classes
     * @param bool   $dismissible dismissible
     *
     * @return self
     */
    public function addMessage($text, $type = self::TYPE_INFO, array $cssClasses = [], $dismissible = true)
    {
        if ($dismissible) {
            $cssClasses[] = 'is-dismissible';
        }
        $this->messages[] = (object) [
            'cssClasses' => $cssClasses,
            'text' => $text,
            'type' => $type,
        ];

        return $this;
    }

    /**
     * Get messages
     *
     * @param bool $keepMessages keep messages
     *
     * @return array
     */
    public function getMessages($keepMessages = false)
    {
        $messages = $this->messages;
        if ($keepMessages) {
            $this->messages = [];
        }

        return $messages;
    }
}
