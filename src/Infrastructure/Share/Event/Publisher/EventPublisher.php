<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Infrastructure\Share\Event\Publisher;

use Broadway\Domain\DomainMessage;

interface EventPublisher
{
    public function handle(DomainMessage $message): void;

    public function publish(): void;
}
