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

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getProjectDir()
    {
        return \dirname(__DIR__, 5);
    }

    public function registerBundles()
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        $contents[FrameworkBundle::class] = ['all' => true];
        $contents[SecurityBundle::class] = ['all' => true];
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    /**
     * @throws \Exception
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->registerExtension(new CoreExtension());
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';
        $loader->load(\dirname(__DIR__).'/Config/services/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/packages/*'.self::CONFIG_EXTS, 'glob');
        if (is_dir($confDir.'/packages/'.$this->environment)) {
            $loader->load($confDir.'/packages/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        }
        $loader->load($confDir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/services_'.$this->environment.self::CONFIG_EXTS, 'glob');
        $loader->load($this->getProjectDir().'/src/**/Config/services/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($this->getProjectDir().'/src/**/Config/services/'.$this->environment.'/*'.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $confDir = $this->getProjectDir().'/config';
        if (is_dir($confDir.'/routes/')) {
            $routes->import($confDir.'/routes/*'.self::CONFIG_EXTS, 'glob');
        }
        if (is_dir($confDir.'/routes/'.$this->environment)) {
            $routes->import($confDir.'/routes/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        }
        $routes->import($confDir.'/routes'.self::CONFIG_EXTS, 'glob');
        $routes->import($this->getProjectDir().'/src/**/Config/routes/*'.self::CONFIG_EXTS, 'glob');
        $routes->import($this->getProjectDir().'/src/**/Config/routes/'.$this->environment.'/*'.self::CONFIG_EXTS, 'glob');
    }

    public static function getConfig(): array
    {
        return CoreExtension::getConfig();
    }
}
