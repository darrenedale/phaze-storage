<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;

class LeaseDurationType implements StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const Infinite = "infinite";

    public const Fixed = "fixed";

    private string $duration;

    public function __construct(string $duration)
    {
        if (self::Infinite !== $duration && self::Fixed !== $duration) {
            throw new InvalidValueException("Expected valid lease duration type, found \"{$duration}\"");
        }

        $this->duration = $duration;
    }

    public function duration(): string
    {
        return $this->duration;
    }

    public function toString(): string
    {
        return $this->duration();
    }
}
