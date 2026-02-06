<?php

declare(strict_types=1);

namespace Phaze\Storage\Types;

final class ListBlobsIncludeDatasets extends AbstractDatasetsType
{
    public const Deleted = 0x0001;

    public const Metadata = 0x0002;

    public const UncommittedBlobs = 0x0004;

    public const Copy = 0x0008;

    public const Tags = 0x0010;

    public const Versions = 0x0020;

    public const DeletedWithVersions = 0x0040;

    public const ImmutabilityPolicy = 0x0080;

    public const LegalHold = 0x0100;

    public const Permissions = 0x0200;

    public const All = 0x1fff;

    protected const ValidMask = self::All;

    public static function all(): ListBlobsIncludeDatasets
    {
        return new ListBlobsIncludeDatasets(self::All);
    }

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

        if ($datasets & self::UncommittedBlobs) {
            $string .= "uncommittedblobs,";
        }

        if ($datasets & self::Copy) {
            $string .= "copy,";
        }

        if ($datasets & self::Tags) {
            $string .= "tags,";
        }

        if ($datasets & self::Versions) {
            $string .= "versions,";
        }

        if ($datasets & self::DeletedWithVersions) {
            $string .= "deletedwithversions,";
        }

        if ($datasets & self::ImmutabilityPolicy) {
            $string .= "immutabilitypolicy,";
        }

        if ($datasets & self::LegalHold) {
            $string .= "legalhold,";
        }

        if ($datasets & self::Permissions) {
            $string .= "permissions,";
        }

        return ("" === $string ? "" : substr($string, 0, -1));
    }
}
