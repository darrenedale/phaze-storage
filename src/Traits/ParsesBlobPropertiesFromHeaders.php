<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits;

use Phaze\Common\Exceptions\ResponseException;
use Phaze\Common\Types\UnsignedInteger;
use Phaze\Storage\Constants;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Responses\GetBlobProperties as GetBlobPropertiesResponse;
use Phaze\Storage\Types\CopyProgress;
use Phaze\Storage\Types\CopyStatus;
use Phaze\Storage\Types\EncryptionContext;
use Phaze\Storage\Types\EncryptionScope;
use Phaze\Storage\Types\ImmutabilityPolicyMode;
use Phaze\Storage\Types\LeaseDurationType;
use Phaze\Storage\Types\LeaseState;
use Phaze\Storage\Types\LeaseStatus;
use Phaze\Storage\Types\Permissions;
use Phaze\Storage\Types\ResourceType;
use Phaze\Storage\Types\TagCount;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\Response\readSingleHeader;
use function Phaze\Common\Utilities\String\parseBoolean;
use function Phaze\Common\Utilities\String\parseDateTime;
use function Phaze\Common\Utilities\String\parseInt;
use function Phaze\Common\Utilities\String\unquote;

trait ParsesBlobPropertiesFromHeaders
{
    /**
     * @template T of GetBlobPropertiesResponse
     * @param T $blob
     * @param ResponseInterface $response
     * @return T
     */
    static protected function parseBlobPropertyHeaders(GetBlobPropertiesResponse $blob, ResponseInterface $response): GetBlobPropertiesResponse
    {
        try {
            if ($response->hasHeader("Content-Length")) {
                $blob = $blob->withContentLength(new UnsignedInteger(parseInt(readSingleHeader($response, "Content-Length"))));
            }

            if ($response->hasHeader("Content-Length")) {
                $blob = $blob->withContentType(readSingleHeader($response, "Content-Type"));
            }

            if ($response->hasHeader("Content-Length")) {
                $blob = $blob->withCreationTime(parseDateTime(readSingleHeader($response, Constants::HeaderCreationTime)));
            }

            if ($response->hasHeader("Content-Length")) {
                $blob = $blob->withLastModified(parseDateTime(readSingleHeader($response, "Last-Modified")));
            }

            if ($response->hasHeader("ETag")) {
                $blob = $blob->withEtag(unquote(readSingleHeader($response, "ETag")));
            }

            $md5 = base64_decode(readSingleHeader($response, "Content-MD5"));

            if (false !== $md5) {
                $blob = $blob->withContentMd5($md5);
            }

            if ($response->hasHeader(Constants::HeaderBlobContentMd5)) {
                $md5 = base64_decode(readSingleHeader($response, Constants::HeaderBlobContentMd5));

                if (false !== $md5) {
                    $blob = $blob->withFullBlobMd5($md5);
                }
            }

            if ($response->hasHeader("Content-Encoding")) {
                $blob = $blob->withContentEncoding(readSingleHeader($response, "Content-Encoding"));
            }

            if ($response->hasHeader("Content-Language")) {
                $blob = $blob->withContentLanguage(readSingleHeader($response, "Content-Language"));
            }

            if ($response->hasHeader("Content-Disposition")) {
                $blob = $blob->withContentDisposition(readSingleHeader($response, "Content-Disposition"));
            }

            if ($response->hasHeader(Constants::HeaderContentCrc64)) {
                $blob = $blob->withContentMd5(readSingleHeader($response, Constants::HeaderContentCrc64));
            }

            if ($response->hasHeader(Constants::HeaderAccessTime)) {
                $blob = $blob->withLastAccessed(parseDateTime(readSingleHeader($response, Constants::HeaderAccessTime)));
            }

            if ($response->hasHeader(Constants::HeaderVersionId)) {
                $blob = $blob->withVersionId(readSingleHeader($response, Constants::HeaderVersionId));
            }

            if ($response->hasHeader(Constants::HeaderIsCurrentVersion)) {
                $blob = $blob->withCurrentVersion(parseBoolean(readSingleHeader($response, Constants::HeaderIsCurrentVersion)));
            }

            if ($response->hasHeader(Constants::HeaderLeaseStatus)) {
                $blob = $blob->withLeaseStatus(new LeaseStatus(readSingleHeader($response, Constants::HeaderLeaseStatus)));
            }

            if ($response->hasHeader(Constants::HeaderLeaseState)) {
                $blob = $blob->withLeaseState(new LeaseState(readSingleHeader($response, Constants::HeaderLeaseState)));
            }

            if ($response->hasHeader(Constants::HeaderServerEncrypted)) {
                $blob = $blob->withServerEncrypted(parseBoolean(readSingleHeader($response, Constants::HeaderServerEncrypted)));
            }

            if ($response->hasHeader(Constants::HeaderEncryptionKeySha256)) {
                $blob = $blob->withCustomerProvidedKeySha256(readSingleHeader($response, Constants::HeaderEncryptionScope));
            }

            if ($response->hasHeader(Constants::HeaderEncryptionScope)) {
                $blob = $blob->withEncryptionScope(new EncryptionScope(readSingleHeader($response, Constants::HeaderEncryptionScope)));
            }

            if ($response->hasHeader(Constants::HeaderEncryptionContext)) {
                $blob = $blob->withEncryptionContext(new EncryptionContext(readSingleHeader($response, Constants::HeaderEncryptionContext)));
            }

            // hierarchical storage only
            if ($response->hasHeader(Constants::HeaderResourceType)) {
                $blob = $blob->withResourceType(new ResourceType(readSingleHeader($response, Constants::HeaderResourceType)));
            }

            // hierarchical storage only
            if ($response->hasHeader(Constants::HeaderOwner)) {
                $blob = $blob->withOwner(readSingleHeader($response, Constants::HeaderOwner));
            }

            // hierarchical storage only
            if ($response->hasHeader(Constants::HeaderGroup)) {
                $blob = $blob->withGroup(readSingleHeader($response, Constants::HeaderGroup));
            }

            // hierarchical storage only
            if ($response->hasHeader(Constants::HeaderPermissions)) {
                $blob = $blob->withPermissions(new Permissions(readSingleHeader($response, Constants::HeaderPermissions)));
            }

            if ($response->hasHeader(Constants::HeaderLeaseDuration)) {
                $blob = $blob->withLeaseDuration(new LeaseDurationType(readSingleHeader($response, Constants::HeaderLeaseDuration)));
            }

            if ($response->hasHeader(Constants::HeaderBlobSequenceNumber)) {
                $blob = $blob->withSequenceNumber(parseInt(readSingleHeader($response, Constants::HeaderPermissions)));
            }

            if ($response->hasHeader(Constants::HeaderCopyId)) {
                $blob = $blob->withCopyId(readSingleHeader($response, Constants::HeaderCopyId));
            }

            if ($response->hasHeader(Constants::HeaderCopyCompletionTime)) {
                $blob = $blob->withCopyCompletionTime(parseDateTime(readSingleHeader($response, Constants::HeaderCopyCompletionTime)));
            }

            if ($response->hasHeader(Constants::HeaderCopyStatusDescription)) {
                $blob = $blob->withCopyStatusDescription(readSingleHeader($response, Constants::HeaderCopyStatusDescription));
            }

            if ($response->hasHeader(Constants::HeaderCopyProgress)) {
                $blob = $blob->withCopyProgress(CopyProgress::parse(readSingleHeader($response, Constants::HeaderCopyProgress)));
            }

            if ($response->hasHeader(Constants::HeaderCopySource)) {
                $blob = $blob->withCopySource(readSingleHeader($response, Constants::HeaderCopySource));
            }

            if ($response->hasHeader(Constants::HeaderCopyStatus)) {
                $blob = $blob->withCopyStatus(new CopyStatus(readSingleHeader($response, Constants::HeaderCopyStatus)));
            }

            if ($response->hasHeader(Constants::HeaderImmutabilityPolicyMode)) {
                $blob = $blob->withImmutabilityPolicyMode(new ImmutabilityPolicyMode(readSingleHeader($response, Constants::HeaderImmutabilityPolicyMode)));
            }

            if ($response->hasHeader(Constants::HeaderImmutabilityPolicyUntilDate)) {
                $blob = $blob->withImmutabilityPolicyUntil(parseDateTime(readSingleHeader($response, Constants::HeaderImmutabilityPolicyUntilDate), DATE_RFC1123));
            }

            if ($response->hasHeader(Constants::HeaderLegalHold)) {
                $blob = $blob->withLegalHold(parseBoolean(readSingleHeader($response, Constants::HeaderLegalHold)));
            }

            // append blobs only
            if ($response->hasHeader(Constants::HeaderBlobCommittedBlockCount)) {
                $blob = $blob->withCommittedBlockCount(new UnsignedInteger(parseInt(readSingleHeader($response, Constants::HeaderBlobCommittedBlockCount))));
            }

            // append blobs only
            if ($response->hasHeader(Constants::HeaderBlobSealed)) {
                $blob = $blob->withSealed(parseBoolean(readSingleHeader($response, Constants::HeaderBlobSealed)));
            }

            if ($response->hasHeader(Constants::HeaderTagCount)) {
                $blob = $blob->withTagCount(new TagCount(parseInt(readSingleHeader($response, Constants::HeaderTagCount))));
            }
        } catch (ResponseException $err) {
            throw new BlobStorageException("Expected unique response header not found: {$err->getMessage()}", previous: $err);
        }

        return $blob;
    }
}
