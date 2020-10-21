<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Application\Decorator;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RequiredPropertyDecorator implements NormalizerInterface
{
    /** @var NormalizerInterface */
    private NormalizerInterface $decorated;

    /**
     * SwaggerDecorator constructor.
     */
    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @param mixed  $object
     * @param string $format
     *
     * @throws ExceptionInterface
     *
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);
        foreach ($docs['components']['schemas'] ?? [] as $schemaName => &$schema) {
            if ('object' === $schema['type'] && isset($schema['properties'])) {
                foreach ($schema['properties'] as $propertyName => $property) {
                    if (isset($property['ignore_required'])) {
                        unset($property['ignore_required']);
                    } elseif (':jsonld' === substr($schemaName, -7)) {
                        $schema['required'][] = $propertyName;
                    }
                }
            }
        }

        return $docs;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
