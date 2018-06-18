<?php

namespace Lullabot\Mpx\DataService;

interface DateTimeFormatInterface
{
    /**
     * Returns date formatted according to given format.
     *
     * @param string $format
     *
     * @return string
     *
     * @see http://php.net/manual/en/datetime.format.php
     */
    public function format(string $format): string;
}
