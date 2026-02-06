<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class LeaseBlobAction implements Stringable, StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const Acquire = "acquire";

    public const Renew = "renew";

    public const Change = "change";

    public const Release = "release";

    public const Break = "break";

    private string $mode;

    public function __construct(string $mode)
    {
        if (self::Acquire !== $mode && self::Renew !== $mode && self::Change !== $mode && self::Release !== $mode && self::Break !== $mode) {
            throw new InvalidValueException("Expected valid lease blob mode, found \"{$mode}\"");
        }

        $this->mode = $mode;
    }

    public function action(): string
    {
        return $this->mode;
    }

    public function toString(): string
    {
        return $this->action();
    }
}
