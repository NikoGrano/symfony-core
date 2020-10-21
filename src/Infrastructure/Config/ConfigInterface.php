<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Infrastructure\Config;

use App\Core\Domain\Shared\Exception\Config\ConfigNotFoundException;

interface ConfigInterface
{
    /**
     * Returns configuration value by id from __.
     *
     * @param mixed $default
     *
     * @return string|int|bool|array|null
     */
    public function get(string $key, $default);

    /**
     * Returns configuration value by id from __.
     * Will throw exception if not found.
     *
     * @throws ConfigNotFoundException
     *
     * @return string|int|bool|array|null
     */
    public function getOrThrow(string $key);

    /**
     * Will return all configuration values.
     */
    public function all(): array;

    /**
     * Returns configuration value by id from full config.
     *
     * @param mixed $default
     *
     * @return string|int|bool|array|null
     */
    public function getGlobal(string $key, $default);

    /**
     * Returns configuration value by id from full config.
     * Will throw exception if not found.
     *
     * @throws ConfigNotFoundException
     *
     * @return string|int|bool|array|null
     */
    public function getGlobalOrThrow(string $key);

    /**
     * Returns full configuration.
     *
     * @param string $key
     *
     * @throws ConfigNotFoundException
     *
     * @return string|int|bool|array|null
     */
    public function allGlobals(): array;
}
