<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Infrastructure\Share\Event\DependencyInjection;

final class DatabaseEventStoreChain
{
    private array $stores = [];

    public function addStore($store): void
    {
        $this->stores = $store;
    }
}
