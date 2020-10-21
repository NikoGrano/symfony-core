<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Infrastructure\Share\Event\Metadata;

use Broadway\Domain\Metadata;
use Broadway\EventSourcing\MetadataEnrichment\MetadataEnricher;

interface MetadataCollection
{
    public function with(Metadata $metadata): self;

    /**
     * @return MetadataEnricher[]
     */
    public function toArray(): array;
}
