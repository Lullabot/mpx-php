<?php

namespace Lullabot\Mpx\DataService;

interface ConcreteDateTimeInterface extends DateTimeFormatInterface
{
    /**
     * @return \DateTime
     */
    public function getDateTime();
}
