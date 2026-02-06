<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Intable as IntableContract;
use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

abstract class AbstractDatasetsType implements IntableContract, StringableContract, Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    protected const ValidMask = 0x00;

    protected int $datasets;

    public function __construct(int $datasets = 0)
    {
        if (0 === $datasets) {
            $this->datasets = 0;
            return;
        }

        self::checkDatasets($datasets);
        $this->datasets = $datasets;
    }

    protected static function checkDatasets(int $datasets): void
    {
        // must have one valid bit set, and must not have any invalid bits set
        if ((0 === $datasets & static::ValidMask) || (0 !== $datasets & ~static::ValidMask)) {
            throw new InvalidValueException(sprintf("Expected valid dataset flag(s), found %08x", $datasets));
        }
    }

    public function datasets(): int
    {
        return $this->datasets;
    }

    public function hasDatasets(int $datasets): bool
    {
        static::checkDatasets($datasets);
        return (bool) ($this->datasets() & $datasets);
    }

    public function withDatasets(int $datasets): self
    {
        static::checkDatasets($datasets);
        $clone = clone $this;
        $clone->datasets |= $datasets;
        return $clone;
    }

    public function withoutDatasets(int $datasets): self
    {
        static::checkDatasets($datasets);
        $clone = clone $this;
        $clone->datasets &= ~$datasets;
        return $clone;
    }

    public function cleared(): self
    {
        $clone = clone $this;
        $clone->datasets = 0;
        return $clone;
    }

    public function toInt(): int
    {
        return $this->datasets();
    }

    public function toString(): string
    {
        return sprintf("%08x", $this->datasets());
    }
}
