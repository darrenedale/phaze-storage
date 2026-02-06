<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Types\UnsignedInteger;

class Timeout extends UnsignedInteger
{
    public const Max = 30;

    public function __construct(int $timeout)
    {
        if (0 > $timeout || self::Max < $timeout) {
            throw new InvalidValueException("Expected timeout in the range 0.." . self::Max . " seconds, found {$timeout}");
        }

        parent::__construct($timeout);
    }

    public function timeout(): int
    {
        return $this->value();
    }
}
