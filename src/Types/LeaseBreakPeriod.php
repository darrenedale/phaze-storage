<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Types\UnsignedInteger;

class LeaseBreakPeriod extends UnsignedInteger
{
    public const Max = 60;

    public function __construct(int $period)
    {
        if (0 > $period || self::Max < $period) {
            throw new InvalidValueException("Expected break period in the range 0.." . self::Max . " seconds, found {$period}");
        }

        parent::__construct($period);
    }

    public function period(): int
    {
        return $this->value();
    }
}