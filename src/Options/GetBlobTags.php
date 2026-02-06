<?php

declare(strict_types=1);

namespace Phaze\Storage\Options;

use Phaze\Storage\Traits\Options\HasLeaseIdOption;
use Phaze\Storage\Traits\Options\HasSnapshotAndVersionOptions;
use Phaze\Storage\Traits\Options\ExposesAllOptions;
use Phaze\Storage\Traits\Options\FiltersOptionsByKey;
use Phaze\Storage\Traits\Options\HasClientRequestIdOption;
use Phaze\Storage\Traits\Options\HasTimeoutOption;

class GetBlobTags extends AbstractOptions
{
    use HasSnapshotAndVersionOptions;
    use HasLeaseIdOption;
    use HasClientRequestIdOption;
    use HasTimeoutOption;
    use FiltersOptionsByKey;
    use ExposesAllOptions;
}
