<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class CopyStatus implements StringableContract, Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const Pending = "pending";

    public const Success = "success";

    public const Aborted = "aborted";

    public const Failed = "failed";

    private string $status;

    public function __construct(string $status)
    {
        if (self::Pending !== $status && self::Success !== $status && self::Aborted !== $status && self::Failed !== $status) {
            throw new InvalidValueException("Expected valid copy status, found {$status}");
        }

        $this->status = $status;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function toString(): string
    {
        return $this->status();
    }
}
