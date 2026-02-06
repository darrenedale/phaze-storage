<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class RehydratePriority implements Stringable, StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const Standard = "Standard";

    public const High = "High";

    private string $priority;

    public function __construct(string $priority)
    {
        if (self::Standard !== $priority && self::High !== $priority) {
            throw new InvalidValueException("Expected valid rehydration priority, found \"{$priority}\"");
        }

        $this->priority = $priority;
    }

    public function priority(): string
    {
        return $this->priority;
    }

    public function toString(): string
    {
        return $this->priority();
    }
}
