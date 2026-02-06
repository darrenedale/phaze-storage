<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class EncryptionContext implements StringableContract, Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const MaxLength = 1024;

    private string $context;

    public function __construct(string $context)
    {
        if (self::MaxLength < strlen($context)) {
            throw new InvalidValueException(sprintf("Expected encryption contexts of <= 1024 bytes, found %d bytes", strlen($context)));
        }

        $this->context = $context;
    }

    public function context(): string
    {
        return $this->context;
    }

    public function toString(): string
    {
        return $this->context();
    }
}
