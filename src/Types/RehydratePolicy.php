<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class RehydratePolicy implements Stringable, StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const Standard = "Standard";

    public const High = "High";

    private string $policy;

    public function __construct(string $policy)
    {
        if (self::Standard !== $policy && self::High !== $policy) {
            throw new InvalidValueException("Expected valid rehudration policy, found {$policy};");
        }

        $this->policy = $policy;
    }

    public function policy(): string
    {
        return $this->policy;
    }

    public function toString(): string
    {
        return $this->policy();
    }
}
