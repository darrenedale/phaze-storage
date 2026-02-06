<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Options;

use Stringable;

use function Phaze\Common\Utilities\Iterable\map;
use function Phaze\Common\Utilities\Iterable\toArray;

trait NaivelyBuildsOptionsQueryString
{
    /** @return array<string,string|int|float|null|Stringable> */
    abstract public function options(): array;

    public function queryString(): string
    {
        return http_build_query(
            toArray(
                // all options are Stringable
                map($this->options(), fn (mixed $value): string => (string) $value)
            )
        );
    }
}
