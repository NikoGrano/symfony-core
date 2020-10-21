<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Application\Query\Event\GetEvents;

use App\Core\Application\Query\Collection;
use App\Core\Application\Query\QueryHandlerInterface;
use App\Core\Domain\Shared\Event\EventRepositoryInterface;
use App\Core\Domain\Shared\Query\Exception\NotFoundException;

final class GetEventsHandler implements QueryHandlerInterface
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @throws NotFoundException
     */
    public function __invoke(GetEventsQuery $query): Collection
    {
        $result = $this->eventRepository->page($query->page, $query->limit);

        return new Collection($query->page, $query->limit, $result['total'], $result['data']);
    }
}
