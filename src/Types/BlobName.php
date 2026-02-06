<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

use LogicException;
use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class BlobName implements Stringable, StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    public const MinLength = 1;

    public const MaxLength = 1024;

    public const MaxFlatPathSegments = 254;

    // actual limit is 63, but one each is used by account and container
    public const MaxHierarchicalPathSegments = 61;

    public const HintFlat = 0x01;

    public const HintHierarchical = 0x02;

    public const HintStrict = 0x04;

    private string $name;

    public function __construct(string $name, int $hint = 0, string $delimiter = "/")
    {
        assert (0x03 !== $hint & 0x03, new LogicException("Expected one or neither of HintFlat or HintHierarchical, found both"));
        assert (1 === mb_strlen($delimiter), new LogicException("Expected single-character path segment delimiter, found \"{$delimiter}\""));

        $len = mb_strlen($name);

        if (self::MinLength > $len || self::MaxLength < $len) {
            throw new InvalidValueException("Expected blob name between 1 and 1024 characters, found {$len}");
        }

        // MS documentation says these should be avoided:
        // https://learn.microsoft.com/en-us/rest/api/storageservices/naming-and-referencing-containers--blobs--and-metadata
        if ($hint & self::HintStrict) {
            if (str_contains($name, ".{$delimiter}")) {
                throw new InvalidValueException("Expected valid blob name, found path segment ending in '.'");
            }

            if (in_array($name[-1], [".", "/", "\\"])) {
                throw new InvalidValueException("Expected valid blob name, found name ending in one or more '.', '/' or '\\'");
            }
        }

        if ($hint & self::HintHierarchical) {
            $maxSegments = self::MaxHierarchicalPathSegments;
        } else {
            // be liberal and allow flat blob names when no hint is provided
            $maxSegments = self::MaxFlatPathSegments;
        }

        $pathSegments = explode($delimiter, $name);

        if ($maxSegments < count($pathSegments)) {
            throw new InvalidValueException("Expected blob name with maximum {$maxSegments} path segments, found " . count($pathSegments));
        }

        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->name();
    }
}
