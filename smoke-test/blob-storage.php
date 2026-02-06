<?php

declare(strict_types=1);

use Phaze\Common\Authorisation\ClientApplicationCredentials;
use Phaze\Common\Constants;
use Phaze\Common\Types\Guid;
use Phaze\Common\Types\UnsignedInteger;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Options\DeleteBlob as DeleteBlobOptions;
use Phaze\Storage\Options\GetBlob as GetBlobOptions;
use Phaze\Storage\Options\GetBlobMetadata as GetBlobMetadataOptions;
use Phaze\Storage\Options\GetBlobProperties as GetBlobPropertiesOptions;
use Phaze\Storage\Options\GetBlobTags as GetBlobTagsOptions;
use Phaze\Storage\Options\LeaseBlob as LeaseBlobOptions;
use Phaze\Storage\Options\ListBlobs;
use Phaze\Storage\Options\ListContainers;
use Phaze\Storage\Services\Blob as BlobStorage;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\ContainerName;
use Phaze\Storage\Types\DeleteType;
use Phaze\Storage\Types\LeaseBlobAction;
use Phaze\Storage\Types\LeaseDuration;
use Phaze\Storage\Types\ListBlobsIncludeDatasets;
use Phaze\Storage\Types\ListContainersIncludeDatasets;
use Phaze\Storage\Types\Version;

require_once __DIR__ . "/bootstrap.php";

$accountName = $_ENV["PHAZE_SMOKETEST_BLOBSTORAGE_ACCOUNTNAME"] ?? null;
$azureTenant = $_ENV["PHAZE_SMOKETEST_TENANT_ID"] ?? null;
$azureClient = $_ENV["PHAZE_SMOKETEST_APP_ID"] ?? null;
$azureClientSecret = $_ENV["PHAZE_SMOKETEST_APP_SECRET"] ?? null;
$containerName = null;
$restoreVersion = null;
$leaseId = null;
$proposedLeaseId = null;
$blobName = null;
$blobContent = null;
$snapshot = null;
$versionId = null;
$deleteType = null;
$max = null;
$command = null;

$script = array_shift($argv);

while (null !== ($arg = array_shift($argv))) {
    switch ($arg) {
        case "--account":
        case "-n":
            $accountName = array_shift($argv);
            break;

        case "--tenant-id":
        case "-t":
            $azureTenant = array_shift($argv);
            break;

        case "--app-id":
        case "-a":
            $azureClient = array_shift($argv);
            break;

        case "--secret":
        case "-s":
            $azureClientSecret = array_shift($argv);
            break;

        case "--version-id":
            $versionId = array_shift($argv);
            break;

        case "--snapshot":
            $snapshot = array_shift($argv);
            break;

        case "--delete-type":
            $deleteType = array_shift($argv);
            break;

        case "--max":
            $max = filter_var(array_shift($argv), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

            if (null === $max) {
                throw new InvalidArgumentException("--max requires a valid int argument");
            }
            break;

        case "--list-containers":
            $command = "list-containers";
            break;

        case "--create-container":
            $command = "create-container";
            $containerName = array_shift($argv);
            break;

        case "--delete-container":
            $command = "delete-container";
            $containerName = array_shift($argv);
            break;

        case "--restore-container":
            $command = "restore-container";
            $containerName = array_shift($argv);
            $restoreVersion = array_shift($argv);
            break;

        case "--lease-container":
            $command = "lease-container";
            $containerName = array_shift($argv);
            break;

        case "--release-container-lease":
            $command = "release-container-lease";
            $containerName = array_shift($argv);
            $leaseId = array_shift($argv);
            break;

        case "--change-container-lease":
            $command = "change-container-lease";
            $containerName = array_shift($argv);
            $leaseId = array_shift($argv);
            $proposedLeaseId = array_shift($argv);
            break;

        case "--list-blobs":
            $command = "list-blobs";
            $containerName = array_shift($argv);
            break;

        case "--put-blob":
            $command = "put-blob";
            $containerName = array_shift($argv);
            $blobName = array_shift($argv);
            $blobContent = array_shift($argv);
            break;

        case "--get-blob":
            $command = "get-blob";
            $containerName = array_shift($argv);
            $blobName = array_shift($argv);
            break;

        case "--delete-blob":
            $command = "delete-blob";
            $containerName = array_shift($argv);
            $blobName = array_shift($argv);
            break;

        case "--get-blob-properties":
            $command = "get-blob-properties";
            $containerName = array_shift($argv);
            $blobName = array_shift($argv);
            break;

        case "--get-blob-metadata":
            $command = "get-blob-metadata";
            $containerName = array_shift($argv);
            $blobName = array_shift($argv);
            break;

        case "--get-blob-tags":
            $command = "get-blob-tags";
            $containerName = array_shift($argv);
            $blobName = array_shift($argv);
            break;

        case "--lease-blob":
            $command = "lease-blob";
            $containerName = array_shift($argv);
            $blobName = array_shift($argv);
            break;

        case "--snapshot-blob":
            $command = "snapshot-blob";
            $containerName = array_shift($argv);
            $blobName = array_shift($argv);
            break;

        case "--copy-blob":
            $command = "copy-blob";
            $containerName = array_shift($argv);
            $blobName = array_shift($argv);
            $toBlobName = array_shift($argv);
            break;

        default:
            // remaining args are payloads/files to put (so put the arg back on the front)
            array_unshift($argv, $arg);
            break 2;
    }
}

if (null === $accountName) {
    fatal("No account name provided");
}

if (null === $azureTenant) {
    fatal("No tenant ID provided");
}

if (null === $azureClient) {
    fatal("No app ID provided");
}

if (null === $azureClientSecret) {
    fatal("No client secret provided");
}

if (null === $command) {
    fatal("No command flag specified (use one of --list-containers, --create-container, --delete-container, --restore-container)");
}

$blob = new BlobStorage(new AccountName($accountName), new ClientApplicationCredentials($azureTenant, $azureClient, $azureClientSecret));

function listContainers(BlobStorage $blob): void
{
    echo "Containers:\n";

    try {
        $options = (new ListContainers())
            ->withInclude(new ListContainersIncludeDatasets(ListContainersIncludeDatasets::Deleted))
            ->withMaxResults(new UnsignedInteger(2));

        do {
            echo "Fetching ... \n";
            $containers = $blob->listContainers($options);

            foreach ($containers as $container) {
                echo "{$container->name()} [{$container->etag()}] (modified {$container->lastModified()->format(Constants::DateTimeFormat)})";

                if ($container->isDeleted()) {
                    echo " [deleted, Version = \"{$container->version()}\"]";
                }

                echo "\n";
            }

            if (!$containers->isComplete()) {
                $options = $options->withMarker($containers->nextMarker());
            }
        } while (!$containers->isComplete());
    } catch (BlobStorageException $err) {
        echo "Exception listing containers: {$err->getMessage()}\n";
    }
}

function createContainer(BlobStorage $blob, string $name): void
{
    try {
        $blob->createContainer(new ContainerName($name));
        echo "Container {$name} created successfully\n";
    } catch (BlobStorageException $err) {
        echo "Exception creating container {$name}: {$err->getMessage()}\n";
    }
}

function deleteContainer(BlobStorage $blob, string $name): void
{
    try {
        $blob->deleteContainer(new ContainerName($name));
        echo "Container {$name} deleted successfully\n";
    } catch (BlobStorageException $err) {
        echo "Exception deleting container {$name}: {$err->getMessage()}\n";
    }
}

function restoreContainer(BlobStorage $blob, string $name, string $version): void
{
    try {
        $blob->restoreContainer(new ContainerName($name), new Version($version));
        echo "Container {$name} restored successfully\n";
    } catch (BlobStorageException $err) {
        echo "Exception restoring container {$name}: {$err->getMessage()}\n";
    }
}

function leaseContainer(BlobStorage $blob, string $name): void
{
    try {
        $id = $blob->acquireLease(new ContainerName($name));
        echo "Container {$name} leased [lease ID {$id}]\n";
    } catch (BlobStorageException $err) {
        echo "Exception acquiring lease on container {$name}: {$err->getMessage()}\n";
    }
}

function releaseContainerLease(BlobStorage $blob, string $name, string $leaseId): void
{
    try {
        $blob->releaseLease(new ContainerName($name), new Guid($leaseId));
        echo "Container {$name} lease released\n";
    } catch (BlobStorageException $err) {
        echo "Exception releasing lease {$leaseId} on container {$name}: {$err->getMessage()}\n";
    }
}

function changeContainerLease(BlobStorage $blob, string $name, string $leaseId, string $proposedLeaseId): void
{
    try {
        $blob->changeLease(new ContainerName($name), new Guid($leaseId), new Guid($proposedLeaseId));
        echo "Container {$name} lease changed to \"{$proposedLeaseId}\"\n";
    } catch (BlobStorageException $err) {
        echo "Exception changing lease {$leaseId} on container {$name}: {$err->getMessage()}\n";
    }
}

function listBlobs(BlobStorage $blobService, string $containerName, ?int $max = null): void
{
    try {
        $lisBlobsOptions = (new ListBlobs())
            ->withInclude(ListBlobsIncludeDatasets::all()
                ->withoutDatasets(ListBlobsIncludeDatasets::Tags)
                ->withoutDatasets(ListBlobsIncludeDatasets::Permissions)
            );

        if (is_int($max)) {
            $lisBlobsOptions = $lisBlobsOptions->withMaxResults(new UnsignedInteger($max));
        }

        do {
            $blobs = $blobService->listBlobs(new ContainerName($containerName), $lisBlobsOptions);

            echo "Blobs:\n";

            foreach ($blobs as $blob) {
                echo "{$blob->name()} [{$blob->type()}/{$blob->resourceType()} {$blob->etag()}] (created: {$blob->creationTime()?->format(Constants::DateTimeFormat)}, modified {$blob->lastModified()?->format(Constants::DateTimeFormat)})";

                if ($blob->isDeleted()) {
                    echo " [deleted, Version = \"{$blob->versionId()}\"]";
                }

                echo "\n";
            }

            if ($blobs->isComplete()) {
                echo "List is complete\n";
            } else {
                echo "List is incomplete, next marker is {$blobs->nextMarker()}\n";
                $lisBlobsOptions = $lisBlobsOptions->withMarker($blobs->nextMarker());
            }
        } while (!$blobs->isComplete());
    } catch (BlobStorageException $err) {
        echo "Exception listing blobs: {$err->getMessage()}\n";
    }
}

function putBlob(BlobStorage $blob, string $container, string $name, string $content): void
{
    try {
        $blob->putBlob(
            new ContainerName($container),
            new BlobName($name),
            $content,
//            (new PutBlobOptions())
//                ->withTags([
//                    new Tag("bead", "framework"),
//                    new Tag("smoke-test", "true"),
//                ])
        );
        echo "Successfully uploaded blob content for \"{$name}\"\n";
    } catch (BlobStorageException $err) {
        echo "Exception uploading blob content: {$err->getMessage()}\n";
    }
}

function getBlob(BlobStorage $blob, string $container, string $name, ?string $versionId = null, ?string $snapshot = null): void
{
    $options = new GetBlobOptions();

    if (null !== $versionId) {
        $options = $options->withVersionId($versionId);
    }

    if (null !== $snapshot) {
        $options = $options->withSnapshot($snapshot);
    }

    try {
        $content = $blob->getBlob(new ContainerName($container), new BlobName($name), $options);
        echo "Successfully retrieved blob {$content->name()} [{$content->type()}/{$content->resourceType()} {$content->etag()}]:\n";
        echo "{$content}\n";
    } catch (BlobStorageException $err) {
        echo "Exception downloading blob content: {$err->getMessage()}\n";
    }
}

function deleteBlob(BlobStorage $blob, string $container, string $name, ?string $versionId = null, ?string $snapshot = null, ?string $deleteType = null): void
{
    $options = new DeleteBlobOptions();

    if (null !== $versionId) {
        $options = $options->withVersionId($versionId);
    }

    if (null !== $snapshot) {
        $options = $options->withSnapshot($snapshot);
    }

    if (null !== $deleteType) {
        $options = $options->withDeleteType(new DeleteType($deleteType));
    }

    try {
        $type = $blob->deleteBlob(new ContainerName($container), new BlobName($name), $options);
        echo "Successfully {$type}-deleted blob {$name}:\n";
    } catch (BlobStorageException $err) {
        echo "Exception deleting blob: {$err->getMessage()}\n";
    }
}

function getBlobProperties(BlobStorage $blob, string $container, string $name, ?string $versionId = null, ?string $snapshot = null): void
{
    $options = new GetBlobPropertiesOptions();

    if (null !== $versionId) {
        $options = $options->withVersionId($versionId);
    }

    if (null !== $snapshot) {
        $options = $options->withSnapshot($snapshot);
    }

    try {
        $properties = $blob->getBlobProperties(new ContainerName($container), new BlobName($name), $options);
        echo "Successfully retrieved properties for blob {$properties->name()} [{$properties->type()}/{$properties->resourceType()} {$properties->etag()}]:\n";
    } catch (BlobStorageException $err) {
        echo "Exception retrieving blob properties: {$err->getMessage()}\n";
    }
}

function getBlobMetadata(BlobStorage $blob, string $container, string $name, ?string $versionId = null, ?string $snapshot = null): void
{
    $options = new GetBlobMetadataOptions();

    if (null !== $versionId) {
        $options = $options->withVersionId($versionId);
    }

    if (null !== $snapshot) {
        $options = $options->withSnapshot($snapshot);
    }

    try {
        $metadata = $blob->getBlobMetadata(new ContainerName($container), new BlobName($name), $options);
        echo "Successfully retrieved metadata for blob {$name} {$metadata->etag()}]:\n";

        foreach ($metadata as $name => $value) {
            echo "{$name}: {$value}\n";
        }
    } catch (BlobStorageException $err) {
        echo "Exception retrieving blob metadata: {$err->getMessage()}\n";
    }
}

function getBlobTags(BlobStorage $blob, string $container, string $name, ?string $versionId = null, ?string $snapshot = null): void
{
    $options = new GetBlobTagsOptions();

    if (null !== $versionId) {
        $options = $options->withVersionId($versionId);
    }

    if (null !== $snapshot) {
        $options = $options->withSnapshot($snapshot);
    }

    try {
        $tags = $blob->getBlobTags(new ContainerName($container), new BlobName($name), $options);
        echo "Successfully retrieved tags for blob {$name}:\n";

        foreach ($tags as $tag) {
            echo "{$tag->key()}: {$tag->value()}\n";
        }
    } catch (BlobStorageException $err) {
        echo "Exception retrieving blob tags: {$err->getMessage()}\n";
    }
}

function leaseBlob(BlobStorage $blob, string $container, string $name): void
{
    $options = (new LeaseBlobOptions())
        ->withLeaseDuration(new LeaseDuration(20));

    try {
        $leaseInfo = $blob->leaseBlob(new ContainerName($container), new BlobName($name), new LeaseBlobAction(LeaseBlobAction::Acquire), $options);
        echo "Successfully acquired lease \"{$leaseInfo->id()}\" for blob \"{$name}\"\n";
    } catch (BlobStorageException $err) {
        echo "Exception acquiring lease for blob \"{$name}\": {$err->getMessage()}\n";
    }
}

function snapshotBlob(BlobStorage $blob, string $container, string $name): void
{
    try {
        $snapshotInfo = $blob->snapshotBlob(new ContainerName($container), new BlobName($name));
        echo "Successfully created snapshot \"{$snapshotInfo->snapshot()}\" for blob \"{$name}\"\n";
    } catch (BlobStorageException $err) {
        echo "Exception creating snapshot for blob \"{$name}\": {$err->getMessage()}\n";
    }
}

function copyBlob(BlobStorage $blob, string $container, string $fromName, string $toName): void
{
    try {
        $copyInfo = $blob->copyBlob(new ContainerName($container), "/{$blob->account()}/{$container}/{$toName}", new BlobName($fromName));
        echo "Successfully copied blob \"{$fromName}\" to \"{$toName}\"\n";
    } catch (BlobStorageException $err) {
        echo "Exception copying blob \"{$fromName}\" to \"{$toName}\": {$err->getMessage()}\n";
    }
}

match ($command) {
    "list-containers" => listContainers($blob),
    "create-container" => createContainer($blob, $containerName),
    "delete-container" => deleteContainer($blob, $containerName),
    "restore-container" => restoreContainer($blob, $containerName, $restoreVersion),
    "lease-container" => leaseContainer($blob, $containerName),
    "release-container-lease" => releaseContainerLease($blob, $containerName, $leaseId),
    "change-container-lease" => changeContainerLease($blob, $containerName, $leaseId, $proposedLeaseId),
    "list-blobs" => listBlobs($blob, $containerName),
    "put-blob" => putBlob($blob, $containerName, $blobName, $blobContent),
    "get-blob" => getBlob($blob, $containerName, $blobName, $versionId, $snapshot),
    "get-blob-properties" => getBlobProperties($blob, $containerName, $blobName, $versionId, $snapshot),
    "get-blob-metadata" => getBlobMetadata($blob, $containerName, $blobName, $versionId, $snapshot),
    "get-blob-tags" => getBlobTags($blob, $containerName, $blobName, $versionId, $snapshot),
    "delete-blob" => deleteBlob($blob, $containerName, $blobName, $versionId, $snapshot, $deleteType),
    "lease-blob" => leaseBlob($blob, $containerName, $blobName),
    "snapshot-blob" => snapshotBlob($blob, $containerName, $blobName),
    "copy-blob" => copyBlob($blob, $containerName, $blobName, $toBlobName),
};
