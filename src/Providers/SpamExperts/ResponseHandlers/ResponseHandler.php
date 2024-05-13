<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\ResponseHandlers;

use Illuminate\Support\Str;
use Upmind\ProvisionProviders\AutoLogin\Exceptions\CannotParseResponse;
use Upmind\ProvisionProviders\AutoLogin\ResponseHandlers\AbstractHandler;

/**
 * Handler to parse SpamExperts data from a PSR-7 response body.
 */
class ResponseHandler extends AbstractHandler
{
    /**
     * @throws \Upmind\ProvisionProviders\AutoLogin\Exceptions\CannotParseResponse
     */
    public function assertSuccess(): void
    {
        parent::assertSuccess();

        $responseBody = $this->getBody();

        if (Str::containsAll(strtolower($responseBody), ['error', 'credentials', 'incorrect'])) {
            throw new CannotParseResponse('Service authentication failed');
        }
    }
}
