<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Exceptions\InvalidValueException;
use Stringable as StringableContract;

/**
 * Named type to ensure only valid account names are used.
 */
final class AccountName implements StringableContract
{
    public const MinLength = 3;

    public const MaxLength = 24;

    private const Alphabet = "abcdefghijklmnopqrstuvwxyz0123456789";

    private string $account;

    public function __construct(string $account)
    {
        if (self::MinLength > strlen($account) || self::MaxLength < strlen($account) || strlen($account) !== strspn($account, self::Alphabet)) {
            throw new InvalidValueException("Expected valid storage account name, found \"{$account}\"");
        }

        $this->account = $account;
    }

    public function __toString(): string
    {
        return $this->account;
    }
}
