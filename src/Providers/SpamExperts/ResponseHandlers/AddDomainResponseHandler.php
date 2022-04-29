<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\ResponseHandlers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Upmind\ProvisionProviders\AutoLogin\Exceptions\CannotParseResponse;
use Upmind\ProvisionProviders\AutoLogin\Exceptions\OperationFailed;
use Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\Exceptions\ResponseMissingAuthTicket;
use Upmind\ProvisionProviders\AutoLogin\ResponseHandlers\AbstractHandler;

/**
 * Handler to parse the 'Add Domain' result from from a PSR-7 response body.
 */
class AddDomainResponseHandler extends ResponseHandler
{
    /**
     * Assert 'Add Domain' was successful.
     *
     * @throws OperationFailed If "Add Domain" failed
     *
     * @return void
     */
    public function assertSuccess(): void
    {
        try {
            parent::assertSuccess();

            $message = strtolower($this->getBody());

            if (Str::contains($message, 'success:')) {
                return;
            }

            if (Str::contains($message, 'already exists')) {
                throw new CannotParseResponse('Domain name already exists');
            }

            if (Str::containsAll($message, ['domain name', 'is incorrect'])) {
                throw new CannotParseResponse('Service identifier is not a valid domain name');
            }

            throw new CannotParseResponse('Failed to add domain name');
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
