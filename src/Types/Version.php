<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class Version implements StringableContract, Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    private string $version;

    public function __construct(string $version)
    {
        if ("" === trim($version)) {
            throw new InvalidValueException("Expected non-empty version, found \"{$version}\"");
        }

        $this->version = $version;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function toString(): string
    {
        return $this->version();
    }
}
