<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

final class ListContainersIncludeDatasets extends AbstractDatasetsType
{
    public const Deleted = 0x01;

    public const Metadata = 0x02;

    public const System = 0x04;

    protected const ValidMask = self::Deleted | self::Metadata | self::System;

    /**
     * Formats the datasets as required for the URL parameter they're intended to be used with.
     *
     * The string is *not* URL-encoded so that it can eb used with http_build_query(), for example.
     */
    public function toString(): string
    {
        $string = "";
        $datasets = $this->datasets();

        if ($datasets & self::Deleted) {
            $string .= "deleted,";
        }

        if ($datasets & self::Metadata) {
            $string .= "metadata,";
        }

        if ($datasets & self::System) {
            $string .= "system,";
        }

        return ("" === $string ? "" : substr($string, 0, -1));
    }
}
