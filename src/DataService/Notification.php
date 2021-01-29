<?php

namespace Lullabot\Mpx\DataService;

/**
 * Represents an mpx notification.
 *
 * @see https://docs.theplatform.com/help/wsf-subscribing-to-change-notifications
 */
class Notification
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var ObjectInterface
     */
    protected $entry;

    /**
     * @return ObjectInterface
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param ObjectInterface $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * Return if this notification is a sync response, containing only an ID.
     *
     * @see https://docs.theplatform.com/help/wsf-subscribing-to-change-notifications#tp-toc10
     */
    public function isSyncResponse(): bool
    {
        return !($this->method || $this->type || $this->entry);
    }
}
