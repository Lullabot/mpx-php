<?php

namespace Lullabot\Mpx\Encoder;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Encoder for mpx cJSON-formatted data.
 */
class CJsonEncoder extends JsonEncoder
{
    /**
     * {@inheritdoc}
     */
    public function decode(string $data, string $format, array $context = []): mixed
    {
        $decoded = parent::decode($data, $format, $context);
        if (isset($decoded['$xmlns'])) {
            $this->decodeCustomFields($decoded);
        }

        $this->cleanup($decoded);

        return $decoded;
    }

    /**
     * Recursively filter an array, removing null and empty string values.
     *
     * @param array &$data The data to filter.
     */
    protected function cleanup(array &$data)
    {
        foreach ($data as &$value) {
            if (\is_array($value)) {
                $this->cleanup($value);
            }
        }

        $data = array_filter($data, fn ($value) => null !== $value);
    }

    /**
     * Decode custom fields into a format usable by a normalizer.
     *
     * mpx returns custom fields as properties on the data object, prefixed with
     * a namespace identifier. Custom fields are described in a single class,
     * and not by manually extending core data classes. To be able to normalize
     * those fields, they must be moved into a single property.
     *
     * Custom fields are returned in a 'customFields' array, with each value
     * representing one mpx custom field namespace. Within each namespace array
     * there is a 'namespace' key with the fully-qualified mpx namespace URI,
     * and a 'data' key with an array of the custom field values.
     *
     * @param array &$decoded The data to decode.
     */
    protected function decodeCustomFields(&$decoded)
    {
        // @todo This is O(namespaces * entries) and can be optimized.
        foreach ($decoded['$xmlns'] as $prefix => $namespace) {
            if (!isset($decoded['entries'])) {
                $this->decodeObject($prefix, $namespace, $decoded);
                continue;
            }

            foreach ($decoded['entries'] as &$entry) {
                $this->decodeObject($prefix, $namespace, $entry);
            }
        }
    }

    /**
     * Decodes an object's custom fields.
     *
     * @param string $prefix    The prefix of the namespace in the response.
     * @param string $namespace The namespace identifier.
     * @param array  $object    The object data to decode.
     */
    protected function decodeObject($prefix, $namespace, &$object)
    {
        $customFields = ['namespace' => $namespace];
        foreach ($object as $key => $value) {
            if (str_contains($key, $prefix.'$')) {
                $fieldName = substr($key, \strlen($prefix) + 1);
                $customFields['data'][$fieldName] = $value;
            }
        }

        // In the case of an object-list response, namespaces are included for
        // all namespaces in any object in the result set. If a given namespace
        // is not used in a single object, we can skip custom fields entirely.
        if (!empty($customFields['data'])) {
            $object['customFields'][$namespace] = $customFields;
        }
    }
}
