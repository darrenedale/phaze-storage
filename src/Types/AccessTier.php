<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class AccessTier implements StringableContract, Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const Hot = "Hot";

    public const Cool = "Cool";

    public const Cold = "Cold";

    public const Archive = "Archive";

    private string $tier;

    public function __construct(string $tier)
    {
        if (self::Hot !== $tier && self::Cool !== $tier && self::Cold !== $tier && self::Archive !== $tier) {
            throw new InvalidValueException("Expected valid AccessTier string, found \"{$tier}\"");
        }

        $this->tier = $tier;
    }

    public function tier(): string
    {
        return $this->tier;
    }

    public function toString(): string
    {
        return $this->tier();
    }
}
