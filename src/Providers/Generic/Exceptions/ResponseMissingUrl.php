<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Generic\Exceptions;

use Throwable;
use Upmind\ProvisionBase\Exception\ProvisionFunctionError;

/**
 * Response was invalid and/or did not contain a url.
 */
class ResponseMissingUrl extends ProvisionFunctionError
{
    //
}
