<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

final class DeleteType implements Stringable, StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const Permanent = "permanent";

    public const Soft = "soft";

    public function __construct(string $type)
    {
        if (self::Permanent !== $type && self::Soft !== $type) {
            throw new InvalidValueException("Expected valid deletion type, found {$type}");
        }

        $this->type = $type;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function toString(): string
    {
        return $this->type();
    }
}
