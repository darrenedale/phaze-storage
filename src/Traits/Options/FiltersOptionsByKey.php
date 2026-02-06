<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Options;

use LogicException;

use function Phaze\Common\Utilities\Iterable\all;

trait FiltersOptionsByKey
{
    abstract protected function options(): array;

    public function only(array $keys): array
    {
        assert(all($keys, fn (mixed $value, string|int $key): bool => is_string($value)), new LogicException("Expected all keys to be strings, found one or more non-strings"));

        return array_filter(
            $this->options(),
            fn (string $key): bool => in_array($key, $keys),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function except(array $keys): array
    {
        assert(all($keys, fn (mixed $value, string|int $key): bool => is_string($value)), new LogicException("Expected all keys to be strings, found one or more non-strings"));

        return array_filter(
            $this->options(),
            fn (string $key): bool => !in_array($key, $keys),
            ARRAY_FILTER_USE_KEY
        );
    }
}
