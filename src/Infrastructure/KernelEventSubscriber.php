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

use App\Core\Infrastructure\Config\ConfigInterface;
use App\Core\Infrastructure\Singletons\ConfigSingleton;
use App\Core\Infrastructure\Singletons\ContainerSingleton;
use App\Core\Infrastructure\Singletons\EventDispatcherSingleton;
use App\Core\Infrastructure\Singletons\MessengerSingleton;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

final class KernelEventSubscriber implements EventSubscriberInterface
{
    private ContainerInterface $container;
    private EventDispatcherInterface $eventDispatcher;
    private MessageBusInterface $commandBus;
    private MessageBusInterface $queryBus;
    private ConfigInterface $config;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST  => ['registerSingleton', -1500],
            ConsoleEvents::COMMAND => ['registerSingleton', -1500],
        ];
    }

    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher,
        MessageBusInterface $commandBus,
        MessageBusInterface $queryBus,
        ConfigInterface $config
    ) {
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
        $this->config = $config;
    }

    public function registerSingleton($event): void
    {
        ContainerSingleton::register($this->container);
        EventDispatcherSingleton::register($this->eventDispatcher);
        MessengerSingleton::register($this->commandBus, $this->queryBus);
        ConfigSingleton::register($this->config);
    }
}
