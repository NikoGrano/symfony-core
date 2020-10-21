<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Infrastructure\Share\Event\Metadata\Collection;

use App\Core\Infrastructure\Share\Event\Metadata\MetadataCollection;
use Broadway\Domain\Metadata;
use Broadway\EventSourcing\MetadataEnrichment\MetadataEnricher;

final class DefaultMetadataCollection implements MetadataCollection
{
    /**
     * @var MetadataEnricher[]
     */
    private array $meta = [];

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->meta;
    }

    public function with(Metadata $metadata): MetadataCollection
    {
        $this->meta[] = $metadata;

        return $this;
    }
}
