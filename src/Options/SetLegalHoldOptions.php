<?php

declare(strict_types=1);

namespace Phaze\Storage\Options;

use Phaze\Storage\Traits\Options\HasLegalHoldOption;
use Phaze\Storage\Traits\Options\HasSnapshotAndVersionOptions;
use Phaze\Storage\Traits\Options\FiltersOptionsByKey;
use Phaze\Storage\Traits\Options\HasClientRequestIdOption;
use Phaze\Storage\Traits\Options\HasTimeoutOption;

class SetLegalHoldOptions extends AbstractOptions
{
    use HasSnapshotAndVersionOptions;
    use HasTimeoutOption;
    use HasLegalHoldOption;
    use HasClientRequestIdOption;
    use FiltersOptionsByKey;
}
