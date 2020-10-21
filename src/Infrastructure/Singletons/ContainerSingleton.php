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

use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerSingleton
{
    private static ContainerInterface $container;

    public static function register(ContainerInterface $container): void
    {
        if (isset(static::$container)) {
            return;
        }

        static::$container = $container;
    }

    public static function get(): ContainerInterface
    {
        return self::$container;
    }
}
