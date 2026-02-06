<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use LogicException;
use Phaze\Common\Constants;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\BlobInfo;
use Phaze\Storage\Options\ListBlobs as ListBlobsOptions;
use Phaze\Storage\Responses\ListBlobs as ListBlobsResponse;
use Phaze\Storage\Services\AbstractStorageService;
use Phaze\Storage\Traits\ParsesTagsXml;
use Phaze\Storage\Types\AccessTier;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobType;
use Phaze\Storage\Types\ContainerName;
use Phaze\Storage\Types\CopyProgress;
use Phaze\Storage\Types\CopyStatus;
use Phaze\Storage\Types\EncryptionContext;
use Phaze\Storage\Types\EncryptionScope;
use Phaze\Storage\Types\LeaseDurationType;
use Phaze\Storage\Types\LeaseState;
use Phaze\Storage\Types\LeaseStatus;
use Phaze\Storage\Types\Permissions;
use Phaze\Storage\Types\RehydratePriority;
use Phaze\Storage\Types\ResourceType;
use Phaze\Storage\Types\TagCount;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Phaze\Common\Types\UnsignedInteger;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;
use function Phaze\Common\Utilities\Iterable\toArray;
use function Phaze\Common\Utilities\String\parseBoolean;
use function Phaze\Common\Utilities\String\parseDateTime;
use function Phaze\Common\Utilities\String\parseInt;

/**
 * TODO x-ms-client-request-id header
 */
class ListBlobs extends AbstractContainerCommand
{
    use HasNoBody;
    use ParsesTagsXml;

    private ListBlobsOptions $options;

    public function __construct(AccountName $account, ContainerName $container, ?ListBlobsOptions $options = null)
    {
        parent::__construct($account, $container);
        $this->options = $options ?? new ListBlobsOptions();
    }

    public function options(): ListBlobsOptions
    {
        return $this->options;
    }

    public function headers(): array
    {
        return [
            "Date" => currentDateTimeForHeader(),
            "x-ms-version" => AbstractStorageService::VersionDefault,
        ];
    }

    public function uri(): string
    {
        $options = $this->options()->queryString();
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}?restype=container&comp=list" . ("" === $options ? "" : "&{$options}");
    }

    public function method(): string
    {
        return Constants::MethodGet;
    }

    private static function parsePropertiesElement(BlobInfo $blob, DOMElement $element): BlobInfo
    {
        assert ("Properties" === $element->tagName, new LogicException("Expected parsePropertiesElement() to be called with a <Properties> root element, found <{$element->tagName}>"));
        $element = $element->firstElementChild;

        while (null !== $element) {
            switch ($element->tagName) {
                case "Creation-Time":
                    $blob = $blob->withCreationTime(parseDateTime(trim($element->textContent)));
                    break;

                case "Last-Modified":
                    $blob = $blob->withLastModified(parseDateTime(trim($element->textContent)));
                    break;

                case "Expiry-Time":
                    $blob = $blob->withExpiryTime(parseDateTime(trim($element->textContent)));
                    break;

                case "Etag":
                    $blob = $blob->withEtag(trim($element->textContent));
                    break;

                case "LeaseStatus":
                    $blob = $blob->withLeaseStatus(new LeaseStatus(trim($element->textContent)));
                    break;

                case "LeaseState":
                    $blob = $blob->withLeaseState(new LeaseState(trim($element->textContent)));
                    break;

                case "LeaseDuration":
                    $blob = $blob->withLeaseDuration(new LeaseDurationType(trim($element->textContent)));
                    break;

                case "BlobType":
                    $blob = $blob->withType(new BlobType(trim($element->textContent)));
                    break;

                case "CopyStatus":
                    $blob = $blob->withCopyStatus(new CopyStatus(trim($element->textContent)));
                    break;

                case "CopySource":
                    $blob = $blob->withCopySource(trim($element->textContent));
                    break;

                case "CopyProgress":
                    $blob = $blob->withCopyProgress(CopyProgress::parse(trim($element->textContent)));
                    break;

                case "HasImmutabilityPolicy":
                    $blob = $blob->withImmutabilityPolicy(parseBoolean($element->textContent));
                    break;

                case "LegalHold":
                    $blob = $blob->withLegalHold(parseBoolean($element->textContent));
                    break;

                case "ImmutableStorageWithVersioningEnabled":
                    $blob = $blob->withImmutableStorageWithVersioningIsEnabled(parseBoolean($element->textContent));
                    break;

                case "TagCount":
                    $blob = $blob->withTagCount(new TagCount(parseInt(trim($element->textContent))));
                    break;

                case "RemainingRetentionDays":
                    $blob = $blob->withRemainingRetentionDays(new UnsignedInteger(parseInt(trim($element->textContent))));
                    break;

                case "Owner":
                    $blob = $blob->withOwner(trim($element->textContent));
                    break;

                case "Group":
                    $blob = $blob->withGroup(trim($element->textContent));
                    break;

                case "Permissions":
                    $blob = $blob->withPermissions(new Permissions(trim($element->textContent)));
                    break;

                case "Acl":      // access control list
                    // TODO need to research this a bit further - currently it just looks like UNIX permissions
                    //  expressed as user::rwx,group::r-x,other::---
                    break;

                case "ResourceType":
                    $blob = $blob->withResourceType(new ResourceType(trim($element->textContent)));
                    break;

                case "Placeholder":
                    $blob = $blob->withPlaceholder(parseBoolean(trim($element->textContent)));
                    break;

                case "Content-Length":
                    $blob = $blob->withContentLength(new UnsignedInteger(parseInt(trim($element->textContent))));
                    break;

                case "Content-Type":
                    $blob = $blob->withContentType(trim($element->textContent));
                    break;

                case "Content-Encoding":
                    $blob = $blob->withContentEncoding(trim($element->textContent));
                    break;

                case "Content-Disposition":
                    $blob = $blob->withContentDisposition(trim($element->textContent));
                    break;

                case "Content-Language":
                    $blob = $blob->withContentLanguage(trim($element->textContent));
                    break;

                case "Content-CRC64":
                    $crc64 = trim($element->textContent);

                    if ("" !== $crc64) {
                        $crc64 = base64_decode($crc64, true);

                        if (false === $crc64) {
                            throw new BlobStorageException("Expected base64-encoded CRC64 digest, found \"{$element->textContent}\"");
                        }
                    }

                    $blob = $blob->withContentCrc64($crc64);
                    break;

                case "Content-MD5":
                    $md5 = trim($element->textContent);

                    if ("" !== $md5) {
                        $md5 = base64_decode($md5, true);

                        if (false === $md5) {
                            throw new BlobStorageException("Expected base64-encoded MD5 digest, found \"{$element->textContent}\"");
                        }
                    }

                    $blob = $blob->withContentMd5($md5);
                    break;

                case "Cache-Control":
                    $blob = $blob->withCacheControl(trim($element->textContent));
                    break;

                case "x-ms-blob-sequence-number":
                    $blob = $blob->withSequenceNumber(parseInt(trim($element->textContent)));
                    break;

                case "AccessTier":
                    $blob = $blob->withAccessTier(new AccessTier(trim($element->textContent)));
                    break;

                case "CopyId":
                    // TODO does this have any constraints?
                    $blob = $blob->withCopyId(trim($element->textContent));
                    break;

                case "CopyCompletionTime":
                    $blob = $blob->withCopyCompletionTime(parseDateTime(trim($element->textContent)));
                    break;

                case "CopyStatusDescription":
                    $blob = $blob->withCopyStatusDescription(trim($element->textContent));
                    break;

                case "ServerEncrypted":
                    $blob = $blob->withServerEncrypted(parseBoolean(trim($element->textContent)));
                    break;

                case "CustomerProvidedKeySha256":
                    $blob = $blob->withCustomerProvidedKeySha256(trim($element->textContent));
                    break;

                case "EncryptionContext":
                    $blob = $blob->withEncryptionContext(new EncryptionContext(trim($element->textContent)));
                    break;

                case "EncryptionScope":
                    $blob = $blob->withEncryptionScope(new EncryptionScope(trim($element->textContent)));
                    break;

                case "IncrementalCopy":
                    $blob = $blob->withIncrementalCopy(parseBoolean($element->textContent));
                    break;

                case "AccessTierInferred":
                    $blob = $blob->withAccessTierInferred(parseBoolean($element->textContent));
                    break;

                case "AccessTierChangeTime":
                    $blob = $blob->withAccessTierChangeTime(parseDateTime(trim($element->textContent)));
                    break;

                case "DeletedTime":
                    $blob = $blob->withDeletedTime(parseDateTime(trim($element->textContent)));
                    break;

                case "RehydratePriority":
                    $blob = $blob->withRehydratePriority(new RehydratePriority(trim($element->textContent)));
                    break;
            }

            $element = $element->nextElementSibling;
        }

        return $blob;

    }

    private static function parseMetadataElement(BlobInfo $blob, DOMElement $element): BlobInfo
    {
        assert ("Metadata" === $element->tagName, new LogicException("Expected parseMetadataElement() to be called with a <Metadata> root element, found <{$element->tagName}>"));
        $element = $element->firstElementChild;

        while (null !== $element) {
            $blob = $blob->withMetadata($element->tagName, trim($element->textContent));
            $element = $element->nextElementSibling;
        }

        return $blob;
    }

    private static function parseOrMetadataElement(BlobInfo $blob, DOMElement $element): BlobInfo
    {
        // TODO implement - need to find an example of this, it's not explained in the REST API docs AFAICT
        return $blob;
    }

    private static function parseBlobElement(DOMElement $element): BlobInfo
    {
        assert ("Blob" === $element->tagName, new LogicException("Expected parseBlobElement() to be called with a <Blob> root element, found <{$element->tagName}>"));
        $childElement =  $element->firstElementChild;
        $blob = null;

        // look for the name element first so we can construct the Container
        while (null !== $childElement) {
            if ("Name" === $childElement->tagName) {
                $blob = new BlobInfo(trim($childElement->textContent));
                break;
            }

            $childElement = $childElement->nextElementSibling;
        }

        if (!$blob) {
            throw new BlobStorageException("Expected <Name> XML element inside <Blob> element not found");
        }

        $childElement =  $element->firstElementChild;

        while (null !== $childElement) {
            switch ($childElement->tagName) {
                case "Name":
                    break;

                case "Snapshot":
                    $blob = $blob->withSnapshot(trim($childElement->textContent));
                    break;

                case "VersionId":
                    $blob = $blob->withVersionId(trim($childElement->textContent));
                    break;

                case "IsCurrentVersion":
                    $blob = $blob->withCurrentVersion(parseBoolean(trim($childElement->textContent)));
                    break;

                case "Deleted":
                    $blob = $blob->withDeleted(parseBoolean(trim($childElement->textContent)));
                    break;

                case "Properties":
                    $blob = self::parsePropertiesElement($blob, $childElement);
                    break;

                case "Metadata":
                    $blob = self::parseMetadataElement($blob, $childElement);
                    break;

                case "Tags":
                    $blob = $blob->withTags(toArray(self::parseTagsElement($childElement)));
                    break;

                case "OrMetadata":
                    $blob = self::parseOrMetadataElement($blob, $childElement);
                    break;

                default:
                    throw new BlobStorageException("Expected valid XML child element for <Blob>, found <{$childElement->tagName}>");
            }

            $childElement = $childElement->nextElementSibling;
        }

        return $blob;
    }

    private static function parseBlobPrefixElement(DOMElement $element): string
    {
        assert ("BlobPrefix" === $element->tagName, new LogicException("Expected parseBlobPrefixElement() to be called with a <BlobPrefix> root element, found <{$element->tagName}>"));
        $element = $element->firstElementChild;

        while (null !== $element) {
            switch ($element->tagName) {
                case "Name":
                    return trim($element->textContent);

                default:
                    throw new BlobStorageException("Expected <Name> element within <BlobPrefix> element, found <{$element->tagName}>");
            }
        }

        throw new BlobStorageException("Expected <Name> element within <BlobPrefix> element, none found");
    }

    private static function parseBlobsElement(DOMElement $element): array
    {
        assert ("Blobs" === $element->tagName, new LogicException("Expected parseBlobsElement() to be called with a <Blobs> root element, found <{$element->tagName}>"));
        $blobs = [];
        $element =  $element->firstElementChild;
        $blobPrefix = null;

        while (null !== $element) {
            switch ($element->tagName) {
                case "Blob":
                    $blobs[] = self::parseBlobElement($element);
                    break;

                case "BlobPrefix":
                    $blobPrefix = self::parseBlobPrefixElement($element);
                    break;

                default:
                    throw new BlobStorageException("Expected <Blob> XML element, found <{$element->tagName}>");
            }

            $element = $element->nextElementSibling;
        }

        return [$blobPrefix, $blobs];
    }

    public function parseResponse(ResponseInterface $response): ListBlobsResponse
    {
        $doc = new DOMDocument();

        if (!$doc->loadXML((string) $response->getBody())) {
            throw new BlobStorageException("Expected valid XML response to ListBlobs request");
        }

        /** @var DOMElement|null $root */
        $root = $doc->firstElementChild;

        if ("EnumerationResults" !== $root?->tagName) {
            throw new BlobStorageException("Expected <EnumerationResults> root XML element, found {$root?->tagName}");
        }

        /** @var DOMElement|null $element */
        $element = $root->firstElementChild;
        $blobs = null;
        $prefix = null;
        $marker = null;
        $nextMarker = null;
        $maxResults = null;

        while (null !== $element) {
            switch ($element->tagName) {
                case "MaxResults":
                    try {
                        $maxResults = parseInt(trim($element->textContent));
                    } catch (InvalidArgumentException $err) {
                        throw new BlobStorageException("Expected int value for <MaxResults> element content, found {$element->textContent}", previous: $err);
                    }
                    break;

                case "Blobs":
                    [$prefix, $blobs,] = self::parseBlobsElement($element);
                    break;

                case "Marker":
                    $marker = trim($element->textContent);
                    break;

                case "NextMarker":
                    $nextMarker = trim($element->textContent);
                    break;

                default:
                    throw new BlobStorageException("Expected <MaxResults>, <Marker>, <Blobs>, or <NextMarker> XML element, found <{$element->tagName}>");
            }

            $element = $element->nextElementSibling;
        }

        // Blobs and NextMarker are required elements
        if (null === $blobs) {
            throw new BlobStorageException("Expected <Blobs> XML element, none found");
        }

        if (null === $nextMarker) {
            throw new BlobStorageException("Expected <NextMarker> XML element, none found");
        }

        $blobs = new ListBlobsResponse($blobs, $nextMarker);

        if (null !== $marker) {
            $blobs = $blobs->withMarker($marker);
        }

        if (null !== $prefix) {
            $blobs = $blobs->withBlobPrefix($marker);
        }

        return $blobs->withMaxResults($maxResults);
    }
}
