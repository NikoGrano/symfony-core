<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Infrastructure\Share\Event\DependencyInjection\Compiler;

use App\Core\Infrastructure\Share\Event\DependencyInjection\DatabaseEventStoreChain;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class DatabaseEventStoreCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(DatabaseEventStoreChain::class)) {
            return;
        }

        $definition = $container->findDefinition(DatabaseEventStoreChain::class);
        $services = $container->findTaggedServiceIds('events.database');

        foreach ($services as $id => $tags) {
            $definition->addMethodCall('addStore', [new Reference($id)]);
        }
    }
}
