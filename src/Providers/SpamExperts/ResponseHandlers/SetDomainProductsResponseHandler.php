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
 * Handler to parse the 'Set Domain Products' result from from a PSR-7 response body.
 */
class SetDomainProductsResponseHandler extends ResponseHandler
{
    /**
     * Assert 'Set Domain Products' was successful.
     *
     * @throws OperationFailed If "Set Domain Products" failed
     *
     * @return void
     */
    public function assertSuccess(): void
    {
        try {
            parent::assertSuccess();

            $message = strtolower($this->getBody());

            if (Str::startsWith($message, 'success:')) {
                return;
            }

            if (Str::containsAll($message, ['domain', 'doesn\'t exist'])) {
                throw new CannotParseResponse('Domain name doesn\'t exist');
            }

            throw new CannotParseResponse('Failed to set domain name features');
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
