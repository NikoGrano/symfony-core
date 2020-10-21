<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Domain\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerSpecialIdDecorator implements NormalizerInterface
{
    private NormalizerInterface $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        // This allows us generate correctly API client via CLI for Frontend.
        if (\PHP_SAPI === 'cli') {
            foreach ($docs['components']['schemas'] as $schema) {
                if ('object' === $schema['type'] && isset($schema['properties'])) {
                    foreach ($schema['properties'] as $name => $property) {
                        if ('@' === $name[0]) {
                            unset($schema['properties'][$name]);
                            $schema['properties']['atsign_'.substr($name, 1)] = $property;
                        }
                    }
                }
            }
        }

        return $docs;
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
