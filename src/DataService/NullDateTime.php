<?php

namespace Lullabot\Mpx\DataService;

/**
 * Implements an empty date and time.
 */
class NullDateTime implements DateTimeFormatInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(string $format): string
    {
        return '';
    }
}
