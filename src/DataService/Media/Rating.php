<?php

namespace Lullabot\Mpx\DataService\Media;

/**
 * Represents a content advisory rating, for example, an MPAA rating of PG-13.
 *
 * @see https://docs.theplatform.com/help/media-rating-object
 */
class Rating
{
    /**
     * The content's rating.
     *
     * @var string
     */
    protected $rating;

    /**
     * The URI of a rating scheme.
     *
     * @var string
     */
    protected $scheme;

    /**
     * The content's subratings.
     *
     * @var string[]
     */
    protected $subRatings;

    /**
     * Returns the content's rating.
     *
     * @return string
     */
    public function getRating(): string
    {
        return $this->rating;
    }

    /**
     * Set the content's rating.
     *
     * @param string
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * Returns the URI of a rating scheme.
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Set the URI of a rating scheme.
     *
     * @param string
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * Returns the content's subratings.
     *
     * @return string[]
     */
    public function getSubRatings(): array
    {
        return $this->subRatings;
    }

    /**
     * Set the content's subratings.
     *
     * @param string[]
     */
    public function setSubRatings($subRatings)
    {
        $this->subRatings = $subRatings;
    }
}
