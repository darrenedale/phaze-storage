<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class ImmutabilityPolicyMode implements Stringable, StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const Unlocked = "unlocked";

    public const Locked = "locked";

    private string $mode;

    public function __construct(string $mode)
    {
        if (self::Unlocked !== $mode && self::Locked !== $mode) {
            throw new InvalidValueException("Expected valid immutability policy mode, found \"{$mode}\"");
        }

        $this->mode = $mode;
    }

    public function mode(): string
    {
        return $this->mode;
    }

    public function toString(): string
    {
        return $this->mode();
    }
}
