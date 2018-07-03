<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\CustomField;

use Lullabot\Mpx\DataService\Annotation\CustomField;
use Lullabot\Mpx\DataService\CustomFieldInterface;

/**
 * Class SeriesCustomField.
 *
 * @CustomField(
 *     namespace="http://www.example.com/xml",
 *     service="Media Data Service",
 *     objectType="Media",
 * )
 */
class SeriesCustomField implements CustomFieldInterface
{
    /**
     * @var string
     */
    protected $series;

    /**
     * @return string
     */
    public function getSeries(): ?string
    {
        return $this->series;
    }

    /**
     * @param string $series
     */
    public function setSeries(?string $series): void
    {
        $this->series = $series;
    }
}
