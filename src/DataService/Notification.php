<?php

namespace Lullabot\Mpx\DataService;

/**
 * @class   Notification
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
     * @var object
     */
    protected $entry;

    /**
     * @return object
     */
    public function getEntry(): object
    {
        return $this->entry;
    }

    /**
     * @param object $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }
}
