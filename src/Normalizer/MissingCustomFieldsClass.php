<?php

namespace Lullabot\Mpx\Normalizer;

use Lullabot\Mpx\DataService\CustomFieldInterface;

/**
 * Stub class for when no custom fields class exists.
 *
 * mpx may be configured to return custom fields that the consumer has no need
 * or interest in, or that the developer simply hasn't implemented yet. The
 * serializer doesn't know if a given custom field is set until it's actually
 * denormalizing the individual property. This class is used so a value is
 * returned and a notice is logged, alerting the developer to implement the
 * class if they choose.
 */
class MissingCustomFieldsClass implements CustomFieldInterface
{
    public function __construct(string $namespace)
    {
        @trigger_error(sprintf('No custom field class implementation for namespace % was found.'), E_USER_NOTICE);
    }
}
