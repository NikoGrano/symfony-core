<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\UI\Http\Rest\Controller;

use App\Core\UI\Http\Rest\Response\JsonApiFormatter;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractCommandQueryController extends AbstractQueryController
{
    private MessageBusInterface $commandBus;

    public function __construct(
        MessageBusInterface $commandBus,
        MessageBusInterface $queryBus,
        JsonApiFormatter $formatter,
        UrlGeneratorInterface $router
    ) {
        parent::__construct($queryBus, $formatter, $router);
        $this->commandBus = $commandBus;
    }

    protected function exec($command): void
    {
        $this->commandBus->dispatch($command);
    }
}
