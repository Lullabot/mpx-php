<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\CustomField;

use Lullabot\Mpx\DataService\Annotation\CustomField;
use Lullabot\Mpx\DataService\CustomFieldInterface;

/**
 * Class NeverUsedCustomField.
 *
 * @CustomField(
 *     namespace="http://www.example.com/never-used",
 *     service="Media Data Service",
 *     objectType="Media",
 * )
 */
class NeverUsedCustomField implements CustomFieldInterface
{
    /**
     * @var string
     */
    protected $neverUsed;

    public function getNeverUsed(): ?string
    {
        return $this->neverUsed;
    }

    public function setNeverUsed(?string $neverUsed): void
    {
        $this->neverUsed = $neverUsed;
    }
}
