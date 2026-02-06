<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;

class BlobType implements Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const BlockBlob = "BlockBlob";

    public const PageBlob = "PageBlob";

    public const AppendBlob = "AppendBlob";

    private string $type;

    public function __construct(string $type)
    {
        if (self::BlockBlob !== $type && self::PageBlob !== $type && self::AppendBlob !== $type) {
            throw new InvalidValueException("Expected valid blob type, found {$type}");
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