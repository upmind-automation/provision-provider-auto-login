<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Marketgoo\ResponseHandlers;

use Psr\Http\Message\ResponseInterface;
use Upmind\ProvisionProviders\AutoLogin\Exceptions\CannotParseResponse;
use Upmind\ProvisionProviders\AutoLogin\Exceptions\OperationFailed;
use Upmind\ProvisionProviders\AutoLogin\ResponseHandlers\AbstractHandler;

/**
 * Handler to parse Marketgoo data from a PSR-7 response body.
 */
class ResponseHandler extends AbstractHandler
{
    /**
     * @throws OperationFailed If response is an error or body is invalid
     */
    public function assertSuccess(string $name = 'operation'): void
    {
        try {
            if ($this->isError()) {
                switch ($this->response->getStatusCode()) {
                    case 400:
                        throw new CannotParseResponse($this->parseError('Invalid parameters'));
                        break;

                    case 403:
                        throw new CannotParseResponse($this->parseError('API authentication not valid!'));
                        break;

                    case 404:
                        throw new CannotParseResponse($this->parseError('Account not found!'));
                        break;

                    case 409:
                        throw new CannotParseResponse($this->parseError('Conflict!'));
                        break;

                    default:
                        throw new CannotParseResponse($this->parseError("Failed to {$name} account"));
                }
            }
        } catch (CannotParseResponse $e) {
            throw (new OperationFailed($e->getMessage(), 0, $e))
                ->withData([
                    'http_code' => $this->response->getStatusCode(),
                    'content_type' => $this->response->getHeaderLine('Content-Type'),
                    'body' => $this->getBody(),
                ]);
        }
    }

    protected function parseError(string $defaultMessage): ?string
    {
        $this->parseJson();
        $errors = $this->getData('errors');

        if (!isset($errors) || !is_array($errors) || empty($errors)) {
            return null;
        }

        return $errors[0]['detail'] ?? $errors[0]['title'] ?? $defaultMessage;
    }
}
