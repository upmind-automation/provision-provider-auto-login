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
 * Handler to parse the 'Get Domain Products' result from from a PSR-7 response body.
 */
class GetDomainProductsResponseHandler extends ResponseHandler
{
    /**
     * Extract enabled domain products from the response.
     *
     * @throws OperationFailed If domain products be determined
     *
     * @return string[] List of enabled domain products
     */
    public function getProducts(): array
    {
        try {
            $this->assertSuccess();

            $this->parseJson();

            $productList = $this->getData();

            if (!$this->isValidProductList($productList)) {
                throw new CannotParseResponse('Unable to parse domain name products from service response');
            }

            return array_keys( // return product names
                array_filter($productList) // return only enabled
            );
        } catch (CannotParseResponse $e) {
            throw (new OperationFailed($e->getMessage(), 0, $e))
                ->withDebug([
                    'http_code' => $this->response->getStatusCode(),
                    'content_type' => $this->response->getHeaderLine('Content-Type'),
                    'body' => $this->getBody(),
                ]);
        }
    }

    /**
     * Determine whether the given product list is valid.
     *
     * @param string[]|null $productList
     *
     * @return bool
     */
    protected function isValidProductList($productList): bool
    {
        if (!is_array($productList)) {
            return false;
        }

        foreach ($productList as $product => $enabled) {
            if (!is_string($product) || empty($product) || (!is_integer($enabled)) && !is_bool($enabled)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Assert 'Add Domain' was successful.
     *
     * @throws OperationFailed If "Add Domain" failed
     *
     * @return void
     */
    public function assertSuccess(): void
    {
        parent::assertSuccess();

        $body = strtolower($this->getBody());

        if (Str::startsWith($body, 'error:')) {
            if (Str::containsAll($body, ['domain', 'doesn\'t exist'])) {
                throw new CannotParseResponse('Domain name doesn\'t exist');
            }

            throw new CannotParseResponse('Failed to get domain name features');
        }
    }
}
