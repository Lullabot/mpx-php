<?php

namespace Lullabot\Mpx\Normalizer;

use Lullabot\Mpx\DataService\CustomFieldInterface;
use Lullabot\Mpx\DataService\DiscoveredCustomField;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * Normalize custom fields into their implementing classes.
 *
 * To normalize these fields we need context from the whole mpx response which
 * is tricky to pass through the Serializer:
 *   - The array of discovered custom field classes is needed so we can know
 *     what class to denormalize the custom data into.
 *   - The decoded data must be all in a single top-level property, so it can
 *     be denormalized at once.
 *
 * This normalizer requires the response data be altered to meet these
 * requirements, which is done in the CJsonEncoder.
 *
 * @see \Lullabot\Mpx\Encoder\CJsonEncoder
 */
class CustomFieldsNormalizer implements DenormalizerInterface
{
    use SerializerAwareTrait;

    /**
     * CustomFieldsNormalizer constructor.
     *
     * @param DiscoveredCustomField[] $customFields An array of discovered custom field classes, indexed by namespace.
     */
    public function __construct(
        /*
         * The array of discovered custom field classes, indexed by namespace.
         */
        private array $customFields
    ) {
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // This is the annotated custom field class defining all custom fields.
        if (!isset($this->customFields[$data['namespace']])) {
            return new MissingCustomFieldsClass($data['namespace']);
        }

        $concreteClass = $this->customFields[$data['namespace']]->getClass();

        if (!$this->serializer instanceof DenormalizerInterface) {
            throw new LogicException(sprintf('Cannot denormalize class "%s" because injected serializer is not a denormalizer', $class));
        }

        return $this->serializer->denormalize($data['data'], $concreteClass);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return CustomFieldInterface::class == $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [CustomFieldInterface::class => false];
    }
}
