<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Intable as IntContract;
use Phaze\Common\Contracts\Types\Stringable as StringContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class LeaseTime implements IntContract, StringContract, Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    private int $seconds;

    public function __construct(int $seconds)
    {
        if (0 > $seconds) {
            throw new InvalidValueException("Expected positive number of seconds, found {$seconds}");
        }

        $this->seconds = $seconds;
    }

    public function seconds(): int
    {
        return $this->seconds;
    }

    public function toInt(): int
    {
        return $this->seconds();
    }

    public function toString(): string
    {
        return (string) $this->seconds();
    }
}
