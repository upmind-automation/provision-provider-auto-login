<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\Exceptions;

use Upmind\ProvisionBase\Exception\ProvisionFunctionError;

/**
 * Response was invalid and/or did not contain an auth ticket.
 */
class ResponseMissingAuthTicket extends ProvisionFunctionError
{
    //
}
