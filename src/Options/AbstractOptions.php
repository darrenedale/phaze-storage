<?php

declare(strict_types=1);

namespace Phaze\Storage\Options;

use function Phaze\Common\Utilities\String\scrub;

class AbstractOptions
{
    /** @var array<string,mixed> */
    private array $options = [];

    public function option(string $name): mixed
    {
        return $this->options[$name] ?? null;
    }

    public function hasOption(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    final protected function options(): array
    {
        return $this->options;
    }

    final protected function setOption(string $name, mixed $value): void
    {
        $this->options[$name] = $value;
    }

    final protected function clearOption(string $name): void
    {
        unset($this->options[$name]);
    }

    final protected function scrubOption(string $name): void
    {
        if (is_string($this->options[$name] ?? null)) {
            scrub($this->options[$name]);
        }

        unset($this->options[$name]);
    }
}
