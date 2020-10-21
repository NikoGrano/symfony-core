<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Domain\Shared\Specification;

abstract class AbstractSpecification
{
    abstract public function isSatisfiedBy($value): bool;

    final public function not($value): bool
    {
        return !$this->isSatisfiedBy($value);
    }
}
