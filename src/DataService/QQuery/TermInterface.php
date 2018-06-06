<?php

namespace Lullabot\Mpx\DataService\QQuery;

interface TermInterface
{
    /**
     * Render this term as a string.
     *
     * @return string
     */
    public function __toString();

    /**
     * @param bool $wrap
     *
     * @return self
     */
    public function wrapParenthesis($wrap = true);
}
