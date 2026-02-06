<?php

declare(strict_types=1);

namespace Phaze\Storage\Responses;

use Phaze\Storage\Traits\Blob\HasClientRequestId;
use Phaze\Storage\Traits\Blob\HasCopyId;
use Phaze\Storage\Traits\Blob\HasCopyStatus;
use Phaze\Storage\Traits\Blob\HasDate;
use Phaze\Storage\Traits\Blob\HasEtag;
use Phaze\Storage\Traits\Blob\HasLastModified;
use Phaze\Storage\Traits\Blob\HasRequestId;
use Phaze\Storage\Traits\Blob\HasRestApiVersion;
use Phaze\Storage\Traits\Blob\HasVersionId;

class CopyBlob
{
    use HasRequestId;
    use HasRestApiVersion;
    use HasEtag;
    use HasDate;
    use HasLastModified;
    use HasCopyId;
    use HasCopyStatus;
    use HasVersionId;
    use HasClientRequestId;

    public function __construct(string $copyId = "")
    {
        $this->copyId = $copyId;
    }
}
