<?php

namespace Kielabokkie\UberCache\Exceptions;

use Carbon\Carbon;
use Exception;

final class UberCacheException extends Exception
{
    public static function expired(string $key): self
    {
        return new self(
            sprintf('Cache for key "%s" expired before it could be refreshed.', $key)
        );
    }
}
