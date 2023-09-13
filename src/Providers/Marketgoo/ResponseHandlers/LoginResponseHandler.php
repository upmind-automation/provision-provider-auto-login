<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Marketgoo\ResponseHandlers;

use Upmind\ProvisionProviders\AutoLogin\Exceptions\CannotParseResponse;
use Upmind\ProvisionProviders\AutoLogin\Exceptions\OperationFailed;

/**
 * Handler to parse the 'Get Login URL' result from from a PSR-7 response body.
 */
class LoginResponseHandler extends ResponseHandler
{
    /**
     * Handler to obtain login url.
     *
     * @throws OperationFailed If 'Get Login URL' failed
     */
    public function getLoginUrl(): string
    {
        try {
            $this->assertSuccess('login');

            $this->parseJson();
            $data = $this->getData('links');

            if (!isset($data['login'])) {
                throw new CannotParseResponse('Unable to obtain login url');
            }

            return $data['login'];
        } catch (CannotParseResponse $e) {
            throw (new OperationFailed($e->getMessage(), 0, $e))
                ->withData([
                    'http_code' => $this->response->getStatusCode(),
                    'content_type' => $this->response->getHeaderLine('Content-Type'),
                    'body' => $this->getBody(),
                ]);
        }
    }
}
