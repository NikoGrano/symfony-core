<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\UI\Http\Web\Controller;

use App\Core\Domain\Shared\Exception\FailedToDecodeBodyException;
use App\Core\Infrastructure\CoreExtension;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    private MessageBusInterface $commandBus;

    private MessageBusInterface $queryBus;

    private RequestStack $requestStack;

    public function __construct(MessageBusInterface $commandBus, MessageBusInterface $queryBus, RequestStack $requestStack)
    {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
        $this->requestStack = $requestStack;
    }

    protected function run(object $command): void
    {
        $this->commandBus->dispatch($command);
    }

    protected function ask(object $query, bool $resolveReturn = true)
    {
        $return = $this->queryBus->dispatch($query);
        if (true === $resolveReturn) {
            return $return->last(HandledStamp::class)->getResult();
        }

        return $return;
    }

    protected function returnView(array $params = [], ?Response $response = null): Response
    {
        $controller = explode('\\', $this->requestStack->getCurrentRequest()->attributes->get('_controller'));
        $controller = str_replace('Controller', '', end($controller));
        $controller = explode('::', $controller);
        $folder = strtolower($controller[0]);
        $file = $controller[1] ?? 'index';
        $module = explode('\\', static::class)[1];

        // @noinspection PhpTemplateMissingInspection
        return $this->render("@$module/$folder/$file.twig", $params, $response);
    }

    /**
     * @throws FailedToDecodeBodyException
     */
    protected function decodeBody(int $depth = 2): array
    {
        try {
            return json_decode($this->requestStack->getCurrentRequest()->getContent(), true, $depth, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new FailedToDecodeBodyException($e);
        }
    }

    protected function returnForException(\Throwable $e, int $code = 400, string $body = '', bool $setHeader = true): Response
    {
        return new Response($body, $code, ['x-debug' => $setHeader ? $e->getMessage() : 'disabled']);
    }

    protected function getRepository(string $class): ObjectRepository
    {
        return $this->getDoctrine()->getRepository($class);
    }

    protected function findOneBy(string $class, $value, string $key = 'id'): ?object
    {
        return $this->getRepository($class)->findOneBy([$key => $value]);
    }

    protected function getIdFromIRI(string $iri): string
    {
        return substr($iri, strrpos($iri, '/') + 1);
    }

    protected function getConfig(string $key = '', $default = null)
    {
        $data = $this->getParameter(CoreExtension::PARAMETER_KEY);

        if ('' === $key) {
            return $data;
        }

        if (!\count($data)) {
            return $default;
        }

        if (false !== strpos($key, '.')) {
            $keys = explode('.', $key);
            foreach ($keys as $innerKey) {
                if (!isset($data[$innerKey])) {
                    return $default;
                }

                $data = $data[$innerKey];
            }

            return $data;
        }

        return isset($data[$key]) ? $data[$key] : $default;
    }
}
