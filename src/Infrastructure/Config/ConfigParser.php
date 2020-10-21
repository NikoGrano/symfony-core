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
use App\Core\Infrastructure\CoreExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

final class ConfigParser implements ConfigInterface
{
    private ContainerBagInterface $bag;

    public function __construct(ContainerBagInterface $bag)
    {
        $this->bag = $bag;
    }

    public function getOrThrow(string $key)
    {
        self::notEmptyKey($key);
        $config = $this->bag->get(CoreExtension::PARAMETER_KEY);

        if (!\count($config)) {
            return $config;
        }

        if (false !== strpos($key, '.')) {
            $keys = explode('.', $key);
            foreach ($keys as $innerKey) {
                if (!isset($config[$innerKey])) {
                    throw new ConfigNotFoundException();
                }

                $config = $config[$innerKey];
            }

            return $config;
        }

        if ($config[$key]) {
            return $config[$key];
        }

        throw new ConfigNotFoundException();
    }

    public function get(string $key, $default)
    {
        try {
            $data = $this->getOrThrow($key);
        } catch (ConfigNotFoundException $e) {
            return $default;
        }

        return $data;
    }

    public function getGlobal(string $key, $default)
    {
        self::notEmptyKey($key);
        if (!$this->bag->has($key)) {
            return $default;
        }

        return $key;
    }

    public function getGlobalOrThrow(string $key)
    {
        self::notEmptyKey($key);
        $this->hasGlobal($key);

        return $this->bag->get($key);
    }

    public function all(): array
    {
        return $this->bag->get(CoreExtension::PARAMETER_KEY);
    }

    public function allGlobals(): array
    {
        return $this->bag->all();
    }

    private static function notEmptyKey(string $key)
    {
        if ('' === $key) {
            throw new \LogicException('Key cannot be empty.');
        }
    }

    /**
     * @throws ConfigNotFoundException
     */
    private function hasGlobal(string $key)
    {
        if (!$this->bag->has($key)) {
            throw new ConfigNotFoundException('Global is not defined.');
        }
    }
}
