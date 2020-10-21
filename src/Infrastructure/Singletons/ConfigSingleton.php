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

use App\Core\Infrastructure\Config\ConfigInterface;

final class ConfigSingleton
{
    private static ConfigInterface $config;

    public static function register(ConfigInterface $config): void
    {
        if (isset(static::$config)) {
            return;
        }

        static::$config = $config;
    }

    public static function get(): ConfigInterface
    {
        return self::$config;
    }
}
