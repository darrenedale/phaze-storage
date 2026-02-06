<?php

declare(strict_types=1);

namespace Phaze\Storage\Services;

use Phaze\Storage\Contracts\ContentProvider;
use Phaze\Storage\Options\CopyBlob as CopyBlobOptions;
use Phaze\Storage\Options\DeleteBlob as DeleteBlobOptions;
use Phaze\Storage\Options\GetBlob as GetBlobOptions;
use Phaze\Storage\Options\GetBlobMetadata as GetBlobMetadataOptions;
use Phaze\Storage\Options\GetBlobProperties as GetBlobPropertiesOptions;
use Phaze\Storage\Options\GetBlobTags as GetBlobTagsOptions;
use Phaze\Storage\Options\LeaseBlob as LeaseBlobOptions;
use Phaze\Storage\Options\ListBlobs as ListBlobsOptions;
use Phaze\Storage\Options\PutBlob as PutBlobOptions;
use Phaze\Storage\Options\SnapshotBlob as SnapshotBlobOptions;
use Phaze\Storage\Responses\ListContainers as ListContainersResponse;
use Phaze\Storage\Responses\GetBlob as GetBlobResponse;
use Phaze\Storage\Responses\GetBlobMetadata as GetBlobMetadataResponse;
use Phaze\Storage\Responses\GetBlobProperties as GetBlobPropertiesResponse;
use Phaze\Storage\Responses\LeaseBlob as LeaseBlobResponse;
use Phaze\Storage\Responses\ListBlobs as ListBlobsResponse;
use Phaze\Storage\Responses\SnapshotBlob as SnapshotBlobResponse;
use Phaze\Storage\RestCommands\Blob\AcquireLease;
use Phaze\Storage\RestCommands\Blob\ChangeLease;
use Phaze\Storage\RestCommands\Blob\CopyBlob;
use Phaze\Storage\RestCommands\Blob\CreateContainer;
use Phaze\Storage\RestCommands\Blob\DeleteBlob;
use Phaze\Storage\RestCommands\Blob\DeleteContainer;
use Phaze\Storage\RestCommands\Blob\GetBlob;
use Phaze\Storage\RestCommands\Blob\GetBlobMetadata;
use Phaze\Storage\RestCommands\Blob\GetBlobProperties;
use Phaze\Storage\RestCommands\Blob\GetBlobTags;
use Phaze\Storage\RestCommands\Blob\LeaseBlob;
use Phaze\Storage\RestCommands\Blob\ListBlobs;
use Phaze\Storage\RestCommands\Blob\ListContainers;
use Phaze\Storage\RestCommands\Blob\PutBlob;
use Phaze\Storage\RestCommands\Blob\ReleaseLease;
use Phaze\Storage\RestCommands\Blob\RestoreContainer;
use Phaze\Storage\RestCommands\Blob\SnapshotBlob;
use Phaze\Storage\Tag;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\ContainerName;
use Phaze\Storage\Types\DeleteType;
use Phaze\Storage\Types\LeaseBlobAction;
use Phaze\Storage\Types\LeaseDuration;
use Phaze\Storage\Types\Version;
use Phaze\Common\Types\Guid;
use Phaze\Common\Types\Url;
use Stringable;

class Blob extends AbstractStorageService
{
    public function listContainers(?ListContainers $options = null): ListContainersResponse
    {
        return $this->sendCommand(new ListContainers($this->account(), $options));
    }

    public function createContainer(ContainerName $container): void
    {
        $this->sendCommand(new CreateContainer($this->account(), $container));
    }

    public function deleteContainer(ContainerName $container, ?Guid $leaseId = null): void
    {
        $this->sendCommand(new DeleteContainer($this->account(), $container, $leaseId));
    }

    public function restoreContainer(ContainerName $container, Version $version): void
    {
        $this->sendCommand(new RestoreContainer($this->account(), $container, $version));
    }

    public function acquireLease(ContainerName $container, ?LeaseDuration $duration = null): Guid
    {
        return $this->sendCommand(new AcquireLease($this->account(), $container, $duration ?? new LeaseDuration(-1)));
    }

    public function releaseLease(ContainerName $container, Guid $leaseId): void
    {
        $this->sendCommand(new ReleaseLease($this->account(), $container, $leaseId));
    }

    public function changeLease(ContainerName $container, Guid $leaseId, Guid $proposedLeaseId): void
    {
        $this->sendCommand(new ChangeLease($this->account(), $container, $leaseId, $proposedLeaseId));
    }

    public function listBlobs(ContainerName $container, ?ListBlobsOptions $options = null): ListBlobsResponse
    {
        return $this->sendCommand(new ListBlobs($this->account(), $container, $options ?? new ListBlobsOptions()));
    }

    public function putBlob(ContainerName $container, BlobName $name, string|Stringable|ContentProvider $data, ?PutBlobOptions $options = null): void
    {
        $this->sendCommand(new PutBlob($this->account(), $container, $name, $data, $options));
    }

    public function getBlob(ContainerName $container, BlobName $name, ?GetBlobOptions $options = null): GetBlobResponse
    {
        return $this->sendCommand(new GetBlob($this->account(), $container, $name, $options));
    }

    public function deleteBlob(ContainerName $container, BlobName $name, ?DeleteBlobOptions $options = null): DeleteType
    {
        return $this->sendCommand(new DeleteBlob($this->account(), $container, $name, $options));
    }

    public function getBlobProperties(ContainerName $container, BlobName $name, ?GetBlobPropertiesOptions $options = null): GetBlobPropertiesResponse
    {
        return $this->sendCommand(new GetBlobProperties($this->account(), $container, $name, $options));
    }

    public function getBlobMetadata(ContainerName $container, BlobName $name, ?GetBlobMetadataOptions $options = null): GetBlobMetadataResponse
    {
        return $this->sendCommand(new GetBlobMetadata($this->account(), $container, $name, $options));
    }

    /** @return iterable<Tag> */
    public function getBlobTags(ContainerName $container, BlobName $name, ?GetBlobTagsOptions $options = null): iterable
    {
        return $this->sendCommand(new GetBlobTags($this->account(), $container, $name, $options));
    }

    public function leaseBlob(ContainerName $container, BlobName $name, LeaseBlobAction $action, ?LeaseBlobOptions $options = null): LeaseBlobResponse
    {
        return $this->sendCommand(new LeaseBlob($this->account(), $container, $name, $action, $options));
    }

    public function snapshotBlob(ContainerName $container, BlobName $name, ?SnapshotBlobOptions $options = null): SnapshotBlobResponse
    {
        return $this->sendCommand(new SnapshotBlob($this->account(), $container, $name, $options));
    }

    public function copyBlob(ContainerName $container, Url|string $from, BlobName $blobName, ?CopyBlobOptions $options = null): SnapshotBlob
    {
        return $this->sendCommand(new CopyBlob($this->account(), $container, $blobName, $from, $options));
    }
}
