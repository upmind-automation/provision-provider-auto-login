<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Generic\Exceptions;

use Upmind\ProvisionBase\Exception\ProvisionFunctionError;

/**
 * Response was invalid and/or did not contain a username.
 */
class ResponseMissingUsername extends ProvisionFunctionError
{
    //
}
