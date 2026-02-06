<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Types\UnsignedInteger;

class TagCount extends UnsignedInteger
{
    public const Max = 10;

    public function __construct(int $count)
    {
        if (0 > $count && self::Max < $count) {
            throw new InvalidValueException("Expected tag count betwen 0 and " . self::Max . ", found {$count}");
        }

        parent::__construct($count);
    }

    public function count(): int
    {
        return $this->value();
    }
}
