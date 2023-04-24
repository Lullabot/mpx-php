<?php

namespace Lullabot\Mpx\DataService\DateTime;

/**
 * Implements a date and time that is not empty.
 */
class ConcreteDateTime implements ConcreteDateTimeInterface
{
    public function __construct(private readonly \DateTime $dateTime)
    {
    }

    /**
     * Construct a new ConcreteDateTime from a time string.
     *
     * @param string $time
     *
     * @throws \Exception
     *
     * @see http://php.net/manual/en/datetime.construct.php
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
