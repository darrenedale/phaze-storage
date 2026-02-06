<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

final class MetadataName implements Stringable, StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    private string $name;


    public function __construct(string $name)
    {
        // technically names can be any valid c# identifier, which supports unicode and we could encode non-ASCII
        // content according to RFC2047. maybe in a future update
        if (!preg_match("/^[_a-zA-Z][_a-zA-Z0-9]*\$/", $name)) {
            throw new InvalidValueException("Expected Metadata name starting with alpha or _ and consisting entirely of alphanumeric or _ characters, found {$name}");
        }

        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->name();
    }
}
