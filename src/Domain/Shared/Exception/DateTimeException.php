<?php

declare(strict_types=1);

/**
 *
 * === NOTICE OF LICENSE ===
 * This source file is released under MIT license by copyright holders.
 * @copyright 2017-2020 (c) Niko Granö (https://granö.fi)
 *
 */

namespace App\Core\Domain\Shared\Exception;

final class DateTimeException extends Exception
{
    public function __construct(\Exception $e)
    {
        parent::__construct('Datetime malformed or not valid', 500, $e);
    }
}
