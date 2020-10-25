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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MigrationsExtension extends Extension implements PrependExtensionInterface
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function prepend(ContainerBuilder $container)
    {
        $ds = \DIRECTORY_SEPARATOR;
        $dirs = array_flip(glob($this->projectDir."{$ds}src{$ds}**{$ds}Setup{$ds}Upgrade", GLOB_ONLYDIR));
        foreach ($dirs as $key => &$dir) {
            $dir = str_replace(["$this->projectDir{$ds}src", $ds], ['App', '\\'], $key);
        }
        $container->prependExtensionConfig('doctrine_migrations', ['migrations_paths' => array_flip($dirs)]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
    }
}
