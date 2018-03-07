<?php

namespace Lullabot\Mpx;

class EchoLogger extends \Psr\Log\AbstractLogger
{
    protected $ignoreLevels;

    public function __construct(array $ignoreLevels = [])
    {
        $this->ignoreLevels = $ignoreLevels;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = [])
    {
        if (in_array($level, $this->ignoreLevels)) {
            return;
        }

        // build a replacement array with braces around the context keys
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{'.$key.'}'] = $val;
        }

        // interpolate replacement values into the message and return
        $message = strtr($message, $replace);
        echo '['.$level.']: '.$message."\n";
    }
}
