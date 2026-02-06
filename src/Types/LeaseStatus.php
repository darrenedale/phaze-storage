<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class LeaseStatus implements StringableContract, Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const Locked = "locked";

    public const Unlocked = "unlocked";

    private string $status;

    public function __construct(string $status)
    {
        if (self::Locked !== $status && self::Unlocked !== $status) {
            throw new InvalidValueException("Expected valid lease status, found {$status}");
        }

        $this->status = $status;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function toString(): string
    {
        return $this->status();
    }
}
