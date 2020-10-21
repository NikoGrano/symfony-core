<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Infrastructure\Api;

use ApiPlatform\Core\Operation\PathSegmentNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Component\String\Inflector\InflectorInterface;

final class PathSegmentNameGeneratorSlash implements PathSegmentNameGeneratorInterface
{
    private InflectorInterface $inflector;

    public function __construct()
    {
        $this->inflector = new EnglishInflector();
    }

    /**
     * {@inheritdoc}
     */
    public function getSegmentName(string $name, bool $collection = true): string
    {
        return $collection
            ? $this->slashize($this->inflector->pluralize($name)[0])
            : $this->slashize($name);
    }

    private function slashize(string $string): string
    {
        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '/$1', $string));
    }
}
