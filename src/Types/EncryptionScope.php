<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class EncryptionScope implements Stringable, StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    private const Alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    private string $scopeName;

    public function __construct(string $scopeName)
    {
        if (4 > strlen($scopeName) || 63 < strlen($scopeName) || strlen($scopeName) !== strspn($scopeName, self::Alphabet)) {
            throw new InvalidValueException("Expected alphanumeric scope name between 4 and 63 (inclusive) characters long, found \"{$scopeName}\"");
        }

        $this->scopeName = $scopeName;
    }

    public function scopeName(): string
    {
        return $this->scopeName;
    }

    public function toString(): string
    {
        return $this->scopeName();
    }
}
