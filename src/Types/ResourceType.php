<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class ResourceType implements StringableContract, Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const File = "file";

    public const Directory = "directory";

    private string $resourceType;

    public function __construct(string $type)
    {
        if (self::File !== $type && self::Directory !== $type) {
            throw new InvalidValueException("Expected recognised ResourceType, found \"{$type}\"");
        }

        $this->resourceType = $type;
    }

    public function resourceType(): string
    {
        return $this->resourceType;
    }

    public function toString(): string
    {
        return $this->resourceType();
    }
}
