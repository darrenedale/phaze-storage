<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class LeaseState implements StringableContract, Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const Available = "available";

    public const Leased = "leased";

    public const Expired = "expired";

    public const Breaking = "breaking";

    public const Broken = "broken";

    private string $state;

    public function __construct(string $state)
    {
        if (self::Available !== $state && self::Leased !== $state && self::Expired !== $state && self::Breaking !== $state && self::Broken !== $state) {
            throw new InvalidValueException("Expected valid lease state, found {$state}");
        }

        $this->state = $state;
    }

    public function state(): string
    {
        return $this->state;
    }

    public function toString(): string
    {
        return $this->state();
    }
}
