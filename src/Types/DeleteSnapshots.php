<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class DeleteSnapshots implements Stringable, StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const Include = "include";
    public const Only = "only";

    private string $delete;

    public function __constuct(string $delete)
    {
        if (self::Include !== $delete && self::Only !== $delete) {
            throw new InvalidValueException("Expected valid delete snapshots option, found {$delete}");
        }

        $this->delete = $delete;
    }

    public function delete(): string
    {
        return $this->delete;
    }

    public function toString(): string
    {
        return $this->delete();
    }
}
