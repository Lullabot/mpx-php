<?php

namespace Lullabot\Mpx\DataService;

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
