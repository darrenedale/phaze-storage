<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use InvalidArgumentException;
use Phaze\Common\Contracts\Types\Intable as IntableContract;
use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class Permissions implements Stringable, StringableContract, IntableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    // NOTE these are octal literals - we can't use 0o notation since we're targeting 8.0+ and 0o was introduced in 8.1
    public const Read = 04;

    public const Write = 02;

    public const Execute = 01;

    private int $user;

    private int $group;

    private int $other;

    public function __construct(string $permissions)
    {
        if (9 !== strlen($permissions)) {
            throw new InvalidValueException("Expected valid UNIX permissions string, found \"{$permissions}\"");
        }

        try {
            $this->user = self::parseSegment(substr($permissions, 0, 3));
            $this->group = self::parseSegment(substr($permissions, 3, 3));
            $this->other = self::parseSegment(substr($permissions, 6, 3));
        } catch (InvalidArgumentException $err) {
            throw new InvalidValueException("Expected valid UNIX permissions string, found \"{$permissions}\"", previous: $err);
        }
    }

    private static function parseSegment(string $segment): int
    {
        $permissions = 0;

        if ("r" === $segment[0]) {
            $permissions |= self::Read;
        } elseif ("-" !== $segment[0]) {
            throw new InvalidArgumentException("Expected 'r' or '-' for first character in UNIX permissions segment, found {$permissions}");
        }

        if ("w" === $segment[1]) {
            $permissions |= self::Write;
        } elseif ("-" !== $segment[1]) {
            throw new InvalidArgumentException("Expected 'w' or '-' for second character in UNIX permissions segment, found {$permissions}");
        }

        if ("x" === $segment[2]) {
            $permissions |= self::Execute;
        } elseif ("-" !== $segment[2]) {
            throw new InvalidArgumentException("Expected 'x' or '-' for first character in UNIX permissions segment, found {$permissions}");
        }

        return $permissions;
    }

    public function user(): int
    {
        return $this->user;
    }

    public function group(): int
    {
        return $this->group;
    }

    public function other(): int
    {
        return $this->other;
    }

    public function toInt(): int
    {
        return ($this->user() << 6) & ($this->group() << 3) & $this->other();
    }

    public function toString(): string
    {
        return
            ($this->user() & self::Read ? "r" : "-")
            . ($this->user() & self::Write ? "w" : "-")
            . ($this->user() & self::Execute ? "x" : "-")
            . ($this->group() & self::Read ? "r" : "-")
            . ($this->group() & self::Write ? "w" : "-")
            . ($this->group() & self::Execute ? "x" : "-")
            . ($this->other() & self::Read ? "r" : "-")
            . ($this->other() & self::Write ? "w" : "-")
            . ($this->other() & self::Execute ? "x" : "-");
    }
}
