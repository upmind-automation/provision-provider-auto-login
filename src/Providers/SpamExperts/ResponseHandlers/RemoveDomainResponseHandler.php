<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\ResponseHandlers;

use Illuminate\Support\Str;
use Upmind\ProvisionProviders\AutoLogin\Exceptions\CannotParseResponse;
use Upmind\ProvisionProviders\AutoLogin\Exceptions\OperationFailed;

/**
 * Handler to parse the 'Remove Domain' result from from a PSR-7 response body.
 */
class RemoveDomainResponseHandler extends ResponseHandler
{
    /**
     * Assert 'Remove Domain' was successful.
     *
     * @throws OperationFailed If "Remove Domain" failed
     */
    public function assertSuccess(): void
    {
        try {
            parent::assertSuccess();

            $message = strtolower($this->getBody());

            if (Str::contains($message, 'success:')) {
                return;
            }

            if (Str::contains($message, 'no such domain')) {
                throw new CannotParseResponse('Domain name does not exist');
            }

            throw new CannotParseResponse('Failed to remove domain name');
        } catch (CannotParseResponse $e) {
            throw (new OperationFailed($e->getMessage(), 0, $e))
                ->withDebug([
                    'http_code' => $this->response->getStatusCode(),
                    'content_type' => $this->response->getHeaderLine('Content-Type'),
                    'body' => $this->getBody(),
                ]);
        }
    }
}
