<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class CopyProgress implements StringableContract, Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    private int $bytes;

    private int $totalBytes;

    public function __construct(int $bytes, int $total)
    {
        if (0 > $bytes) {
            throw new InvalidValueException("Expected number of bytes >= 0, found {$bytes}");
        }

        if (0 > $total) {
            throw new InvalidValueException("Expected total number of bytes >= 0, found {$total}");
        }

        if ($bytes > $total) {
            throw new InvalidValueException("Expected number of bytes <= total number of bytes, found {$bytes} / {$total}");
        }

        $this->bytes = $bytes;
        $this->totalBytes = $total;
    }

    public function bytes(): int
    {
        return $this->bytes;
    }

    public function totalBytes(): int
    {
        return $this->totalBytes;
    }

    public static function parse(string $copyProgress): self
    {
        if (!str_contains($copyProgress, "/")) {
            throw new InvalidValueException("Expected CopyProgress string, found {$copyProgress}");
        }

        [$bytes, $totalBytes,] = array_map(
            function (string $number): int
            {
                $int = filter_var($number, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

                if (null === $int) {
                    throw new InvalidValueException("Expected integer string in CopyProgress string, found {$number}");
                }

                return $int;
            },
            explode("/", $copyProgress, 2)
        );

        return new self($bytes, $totalBytes);
    }

    public function toString(): string
    {
        return "{$this->bytes()}/{$this->totalBytes()}";
    }
}
