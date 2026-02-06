<?php

declare(strict_types=1);

namespace Phaze\Storage;

final class Constants
{
    // TODO consider moving these to a Headers or HeaderNames class
    public const HeaderRequestId = "x-ms-request-id";

    // the REST API version
    public const HeaderVersion = "x-ms-version";

    public const HeaderClientRequestId = "x-ms-client-request-id";

    public const HeaderBlobType = "x-ms-blob-type";

    // The ID of the version of a blob
    public const HeaderVersionId = "x-ms-version-id";

    public const HeaderIsCurrentVersion = "x-ms-is-current-version";

    public const HeaderSnapshot = "x-ms-snapshot";

    public const HeaderContentCrc64 = "x-ms-content-crc64";

    public const HeaderServerEncrypted = "x-ms-server-encrypted";

    public const HeaderEncryptionKeySha256 = "x-ms-encryption-key-sha256";

    public const HeaderEncryptionKey = "x-ms-encryption-key";

    public const HeaderEncryptionAlgorithm = "x-ms-encryption-algorithm";

    public const HeaderEncryptionScope = "x-ms-encryption-scope";

    public const HeaderEncryptionContext = "x-ms-encryption-context";

    public const HeaderResourceType = "x-ms-resource-type";

    public const HeaderOwner = "x-ms-owner";

    public const HeaderGroup = "x-ms-group";

    public const HeaderPermissions = "x-ms-permissions";

    public const HeaderBlobSequenceNumber = "x-ms-blob-sequence-number";

    public const HeaderTags = "x-ms-tags";

    public const HeaderTagCount = "x-ms-tag-count";

    public const HeaderAccessTier = "x-ms-access-tier";

    public const HeaderLegalHold = "x-ms-legal-hold";

    public const HeaderSealBlob = "x-ms-seal-blob";

    public const HeaderLeaseId = "x-ms-lease-id";

    public const HeaderProposedLeaseId = "x-ms-proposed-lease-id";

    public const HeaderSourceLeaseId = "x-ms-source-lease-id";

    public const HeaderLeaseStatus = "x-ms-lease-status";

    public const HeaderLeaseState = "x-ms-lease-state";

    public const HeaderLeaseDuration = "x-ms-lease-duration";

    public const HeaderLeaseBreakPeriod = "x-ms-lease-break-period";

    public const HeaderLeaseTime = "x-ms-lease-time";

    public const HeaderRangeGetContentMd5 = "x-ms-range-get-content-md5";

    public const HeaderRangeGetContentCrc64 = "x-ms-range-get-content-crc64";

    public const HeaderCreationTime = "x-ms-creation-time";

    public const HeaderAccessTime = "x-ms-last-access-time";

    public const HeaderCopyId = "x-ms-copy-id";

    public const HeaderCopyCompletionTime = "x-ms-copy-completion-time";

    public const HeaderCopyStatusDescription = "x-ms-copy-status-description";

    public const HeaderCopyProgress = "x-ms-copy-progress";

    public const HeaderCopySource = "x-ms-copy-source";

    public const HeaderCopyStatus = "x-ms-copy-status";

    public const HeaderBlobCommittedBlockCount = "x-ms-blob-committed-block-count";

    public const HeaderBlobSealed = "x-ms-blob-sealed";

    public const HeaderBlobContentMd5 = "x-ms-blob-content-md5";

    public const HeaderImmutabilityPolicyMode = "x-ms-immutability-policy-mode";

    public const HeaderImmutabilityPolicyUntilDate = "x-ms-immutability-policy-until-date";

    public const HeaderDeleteTypePermanent = "x-ms-delete-type-permanent";

    public const HeaderDeleteSnapshots = "x-ms-delete-snapshots";

    public const HeaderLeaseBlobAction = "x-ms-lease-action";

    public const HeaderRehydratePolicy = "x-ms-rehydrate-priority";

    public const HeaderPrefixMeta = "x-ms-meta-";
}
