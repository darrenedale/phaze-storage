<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

final class ListBlobsShowOnlyDatasets extends AbstractDatasetsType
{
    public const Deleted = 0x01;

    public const Files = 0x02;

    public const Directories = 0x04;

    protected const ValidMask = 0x07;

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

        if ($datasets & self::Files) {
            $string .= "files,";
        }

        if ($datasets & self::Directories) {
            $string .= "directories,";
        }

        return ("" === $string ? "" : substr($string, 0, -1));
    }
}
