<?php

namespace Lullabot\Mpx\DataService;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Remove null and empty string values from a decoded JSON array.
 */
class CJsonEncoder extends JsonEncoder
{

    /**
     * {@inheritdoc}
     */
    public function decode($data, $format, array $context = array())
    {
        $decoded = parent::decode($data, $format, $context);
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

        return array_filter($data, function($value) {
            return null !== $value && "" != $value;
        });
    }
}
