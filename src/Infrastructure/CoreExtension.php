<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Infrastructure;

use App\Core\Domain\Shared\Config\ConfigurationBridge;
use App\Core\Domain\Shared\Config\ConfiguratorInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class CoreExtension extends Extension
{
    public const PARAMETER_KEY = 'core_sys_config';
    public const CONFIG_KEY = '__';

    public function getAlias(): string
    {
        return self::CONFIG_KEY;
    }

    public function getNamespace(): bool
    {
        return false;
    }

    /**
     * Loads a specific configuration.
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configurators = [];
        foreach (get_declared_classes() as $class) {
            if (\in_array(ConfiguratorInterface::class, class_implements($class), true)) {
                $configurators[] = $class;
            }
        }

        // No Configurators Declared.
        if ([] === $configurators) {
            return;
        }

        $builder = new TreeBuilder(self::CONFIG_KEY);
        /** @var ConfiguratorInterface $configurator */
        foreach ($configurators as $configurator) {
            $builder = $configurator::configure($builder);
        }

        $configuration = new ConfigurationBridge($builder);
        $c = $this->processConfiguration($configuration, $this->mergeDepends($configs));

        $container->setParameter(self::PARAMETER_KEY, $c);
    }

    public function mergeDepends(array $configs): array
    {
        $depends = [];
        foreach ($configs as &$config) {
            if (isset($config['depends'])) {
                $key = array_key_first($config['depends']);
                $depends[$key] = $config['depends'][$key];
                unset($config['depends']);
            }
        }
        $configs[]['depends'] = $depends;

        return $configs;
    }
}
