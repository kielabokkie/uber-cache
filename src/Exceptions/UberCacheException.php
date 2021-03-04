<?php

namespace Kielabokkie\UberCache\Exceptions;

use Carbon\Carbon;
use Exception;

final class UberCacheException extends Exception
{
    public static function expired(string $key, Carbon $maxTtl): self
    {
        return new self(
            sprintf('Cache for key "%s" expired at %s', $key, $maxTtl->toDateTimeLocalString())
        );
    }
}
