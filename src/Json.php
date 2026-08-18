<?php

namespace Lullabot\Mpx;

/**
 * JSON helpers with the error handling mpx code relies on.
 *
 * These replace GuzzleHttp\Utils::jsonDecode() and GuzzleHttp\json_encode(),
 * which are deprecated in guzzlehttp/guzzle 7.15 and removed in 8.0. PHP's
 * JSON_THROW_ON_ERROR is not a drop-in replacement: it throws \JsonException,
 * while callers of this library catch the \InvalidArgumentException that Guzzle
 * threw. These methods keep that contract.
 */
final class Json
{
    /**
     * Decode a JSON string.
     *
     * @param string|\Stringable $json    The JSON to decode.
     * @param bool               $assoc   Return objects as associative arrays.
     * @param int                $depth   The maximum nesting depth.
     * @param int                $options Bitmask of JSON decode options.
     *
     * @throws \InvalidArgumentException Thrown if the JSON cannot be decoded.
     */
    public static function decode($json, bool $assoc = false, int $depth = 512, int $options = 0)
    {
        if ($depth < 1) {
            throw new \InvalidArgumentException('json_decode error: Maximum stack depth exceeded');
        }

        $data = json_decode((string) $json, $assoc, $depth, $options);
        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException('json_decode error: '.json_last_error_msg());
        }

        return $data;
    }

    /**
     * Encode a value as JSON.
     *
     * @param mixed $value   The value to encode.
     * @param int   $options Bitmask of JSON encode options.
     * @param int   $depth   The maximum nesting depth.
     *
     * @throws \InvalidArgumentException Thrown if the value cannot be encoded.
     */
    public static function encode($value, int $options = 0, int $depth = 512): string
    {
        $json = json_encode($value, $options, $depth);
        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException('json_encode error: '.json_last_error_msg());
        }

        return $json;
    }
}
