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

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

abstract class ModuleDependentFixture extends Fixture implements DependentFixtureInterface
{
    // In Memory Caching
    private static ?string $_dir = null;
    private static ?array $_declaredModules = null;

    abstract protected static function modules(): array;

    public function getDependencies(): array
    {
        $deps = [];
        $declared = self::declaredModules();
        foreach (static::modules() as $module) {
            if (!\in_array($module, $declared, true)) {
                throw new \LogicException("Module $module is not installed!");
            }

            $fixtures = glob(self::getDir().'/'.ucfirst($module).'/Setup/Fixtures/*');
            foreach ($fixtures as &$fixture) {
                $fixture = substr($fixture, strrpos($fixture, '/') + 1, -4);
                $fixture = 'App\\'.ucfirst($module)."\Setup\Fixtures\\$fixture";
            }

            $deps = array_merge($deps, $fixtures);
        }

        return $deps;
    }

    private static function declaredModules(): array
    {
        if (null === self::$_declaredModules) {
            self::$_declaredModules = glob(self::getDir().'/*', GLOB_ONLYDIR);
            $normalizer = new CamelCaseToSnakeCaseNameConverter(null, true);
            foreach (self::$_declaredModules as &$module) {
                $module = $normalizer->normalize(substr($module, strrpos($module, '/') + 1));
            }
        }

        return self::$_declaredModules;
    }

    private static function getDir(): string
    {
        if (null === self::$_dir) {
            self::$_dir = \dirname((new \ReflectionClass(self::class))->getFileName(), 4);
        }

        return self::$_dir;
    }
}
