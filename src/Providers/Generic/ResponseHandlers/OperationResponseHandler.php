<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Generic\ResponseHandlers;

use Upmind\ProvisionProviders\AutoLogin\Exceptions\CannotParseResponse;
use Upmind\ProvisionProviders\AutoLogin\Exceptions\OperationFailed;
use Upmind\ProvisionProviders\AutoLogin\ResponseHandlers\AbstractHandler;

/**
 * Handler to determine generic operation success/failure from a PSR-7 response body.
 */
class OperationResponseHandler extends AbstractHandler
{
    /**
     * @throws \Upmind\ProvisionProviders\AutoLogin\Exceptions\OperationFailed
     */
    public function assertOperationSuccess(string $name = 'operation'): void
    {
        try {
            if (is_null($this->getData())) {
                return; // empty response, call it success because we got a 2xx response
            }

            $possibleKeys = [
                'success',
                'result',
            ];

            $successValues = [
                'success',
                'ok',
                true,
                'true',
                1,
                '1'
            ];

            foreach ($possibleKeys as &$key) {
                $value = $this->getData($key);

                if (is_null($value)) {
                    continue;
                }

                if (in_array($value, $successValues, true)) {
                    return; // this looks like a success
                }

                throw new CannotParseResponse(sprintf('%s failed', ucfirst($name)));
            }
            unset($key);
            unset($value);

            throw new CannotParseResponse(sprintf('Unable to parse %s result from service response', $name));
        } catch (CannotParseResponse $e) {
            throw (new OperationFailed($e->getMessage(), 0, $e))
                ->withDebug([
                    'operation' => $name,
                    'http_code' => $this->response->getStatusCode(),
                    'content_type' => $this->response->getHeaderLine('Content-Type'),
                    'body' => $this->getBody(),
                    'result_key' => $key ?? null,
                    'result_value' => $value ?? null,
                ]);
        }
    }
}
