<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Infrastructure\Setup;

use App\Core\Infrastructure\Config\ConfigInterface;
use Doctrine\Migrations\Version\AlphabeticalComparator;
use Doctrine\Migrations\Version\Comparator;
use Doctrine\Migrations\Version\Version;
use MJS\TopSort\Implementations\ArraySort;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class ProjectVersionComparator implements Comparator
{
    private array $dependencies;
    private AlphabeticalComparator $sorter;
    private ConfigInterface $config;
    private string $projectDir;
    private NameConverterInterface $normalizer;

    public function __construct(ConfigInterface $config, string $kernelDir)
    {
        $this->sorter = new AlphabeticalComparator();
        $this->config = $config;
        $this->projectDir = $kernelDir;
        $this->normalizer = (new CamelCaseToSnakeCaseNameConverter(null, true));
        $this->dependencies = $this->buildDependencies();
    }

    private function buildDependencies(): array
    {
        $sorter = new ArraySort();
        $modules = glob($this->projectDir.'/src/*', GLOB_ONLYDIR);
        foreach ($modules as $key => $module) {
            $module = $this->normalizer->normalize(substr($module, strrpos($module, '/') + 1));
            $modules[$module] = $this->config->get("depends.$module", []);
            unset($modules[$key]);
        }
        $modules['core'] = [];

        foreach ($modules as $module => $dependencies) {
            if ('core' !== $module) {
                $dependencies = ['core', ...$dependencies];
            }
            $sorter->add($module, $dependencies);
        }

        return array_flip($sorter->sort());
    }

    private function getModule(Version $version): string
    {
        return $this->normalizer->normalize(explode('\\', (string) $version)[1]);
    }

    public function compare(Version $a, Version $b): int
    {
        $prefixA = $this->getModule($a);
        $prefixB = $this->getModule($b);

        return $this->dependencies[$prefixA] <=> $this->dependencies[$prefixB]
            ?: $this->sorter->compare($a, $b);
    }
}
