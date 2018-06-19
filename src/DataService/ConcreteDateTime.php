<?php

namespace Lullabot\Mpx\DataService;

/**
 * Implements a date and time that is not empty.
 */
class ConcreteDateTime implements ConcreteDateTimeInterface
{
    /**
     * @var \DateTime
     */
    private $dateTime;

    public function __construct(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * Construct a new ConcreteDateTime from a time string.
     *
     * @param string        $time
     * @param \DateTimeZone $timezone
     *
     * @see http://php.net/manual/en/datetime.construct.php
     *
     * @return self
     */
    public static function fromString($time = 'now', \DateTimeZone $timezone = null): self
    {
        return new static(new \DateTime($time, $timezone));
    }

    /**
     * {@inheritdoc}
     */
    public function format(string $format): string
    {
        return $this->dateTime->format($format);
    }

    /**
     * {@inheritdoc}
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }
}
