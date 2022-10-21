<?php

namespace Lullabot\Mpx\Tests;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

interface SerializerDenormalizerInterface extends SerializerInterface, DenormalizerInterface
{
}
