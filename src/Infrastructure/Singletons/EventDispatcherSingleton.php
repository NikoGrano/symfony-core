<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Infrastructure\Singletons;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherSingleton
{
    private static EventDispatcherInterface $eventDispatcher;

    public static function register(EventDispatcherInterface $eventDispatcher): void
    {
        if (isset(static::$eventDispatcher)) {
            return;
        }

        static::$eventDispatcher = $eventDispatcher;
    }

    public static function get(): EventDispatcherInterface
    {
        return self::$eventDispatcher;
    }
}
