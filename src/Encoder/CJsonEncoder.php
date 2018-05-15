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
    public function decode($data, $format, array $context = [])
    {
        $decoded = parent::decode($data, $format, $context);
        $decoded = $this->decodeCustomFields($decoded);

        return $this->cleanup($decoded);
    }

    /**
     * Recursively filter an array, removing null and empty string values.
     *
     * @param array $data The data to filter.
     *
     * @return array The filtered array.
     */
    protected function cleanup(array $data)
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $value = $this->cleanup($value);
            }
        }

        return array_filter($data, function ($value) {
            return null !== $value;
        });
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
     * @param array $decoded The data to decode.
     *
     * @return array The decoded data.
     */
    protected function decodeCustomFields($decoded)
    {
        if (isset($decoded['$xmlns'])) {
            foreach ($decoded['$xmlns'] as $prefix => $namespace) {
                $customFields = ['namespace' => $namespace];
                foreach ($decoded as $key => $value) {
                    if (false !== strpos($key, $prefix.'$')) {
                        $fieldName = substr($key, strlen($prefix) + 1);
                        $customFields['data'][$fieldName] = $value;
                    }
                }
                $decoded['customFields'][] = $customFields;
            }
        }

        return $decoded;
    }
}
