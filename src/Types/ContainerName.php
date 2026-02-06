<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use Phaze\Common\Exceptions\InvalidValueException;
use Stringable as StringableContract;

/**
 * Named type to ensure only valid container names are used.
 */
final class ContainerName implements StringableContract
{
    public const MinLength = 3;

    public const MaxLength = 63;

    private const ContainerPattern = "/^[a-z]([a-z0-9]|[a-z0-9]-[a-z0-9])+\$/";

    private string $container;

    public function __construct(string $container)
    {
        if (self::MinLength > strlen($container) || self::MaxLength < strlen($container) || !preg_match(self::ContainerPattern, $container)) {
            throw new InvalidValueException("Expected valid container name, found \"{$container}\"");
        }

        $this->container = $container;
    }

    public function __toString(): string
    {
        return $this->container;
    }
}
