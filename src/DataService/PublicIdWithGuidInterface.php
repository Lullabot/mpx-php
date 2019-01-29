<?php

namespace Lullabot\Mpx\DataService;

/**
 * Interface for objects containing both a Public ID (pid) and a GUID.
 */
interface PublicIdWithGuidInterface extends PublicIdentifierInterface, GuidInterface
{
}
