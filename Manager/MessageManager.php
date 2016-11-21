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
     * @param string $text text
     *
     * @return self
     */
    public function addInfo($text)
    {
        return $this->addMessage($text, self::TYPE_INFO);
    }

    /**
     * Add success
     *
     * @param string $text text
     *
     * @return self
     */
    public function addSuccess($text)
    {
        return $this->addMessage($text, self::TYPE_SUCCESS);
    }

    /**
     * Add warning
     *
     * @param string $text text
     *
     * @return self
     */
    public function addWarning($text)
    {
        return $this->addMessage($text, self::TYPE_WARNING);
    }

    /**
     * Add error
     *
     * @param string $text text
     *
     * @return self
     */
    public function addError($text)
    {
        return $this->addMessage($text, self::TYPE_ERROR);
    }

    /**
     * Add message
     *
     * @param string $text text
     * @param string $type type
     *
     * @return self
     */
    public function addMessage($text, $type = self::TYPE_INFO)
    {
        $this->messages[] = (object) [
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
