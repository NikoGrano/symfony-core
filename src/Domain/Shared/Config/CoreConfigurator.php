<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Domain\Shared\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class CoreConfigurator implements ConfiguratorInterface
{
    public static function configure(TreeBuilder $builder): TreeBuilder
    {
        $builder->getRootNode()
        ->children()
        ->arrayNode('core')->isRequired()
        ->children()
        ->booleanNode('enabled')->defaultTrue()->end()
        ->end()
        ->end()
        ->end()
        ;

        return $builder;
    }
}
