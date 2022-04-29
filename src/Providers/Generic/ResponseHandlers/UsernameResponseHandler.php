<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Generic\ResponseHandlers;

use Upmind\ProvisionProviders\AutoLogin\Exceptions\CannotParseResponse;
use Upmind\ProvisionProviders\AutoLogin\Providers\Generic\Exceptions\ResponseMissingUsername;
use Upmind\ProvisionProviders\AutoLogin\ResponseHandlers\AbstractHandler;

/**
 * Handler to parse a username from a PSR-7 response body.
 */
class UsernameResponseHandler extends AbstractHandler
{
    /**
     * Extract a username from the response.
     *
     * @throws ResponseMissingUsername If username cannot be determined
     *
     * @param string $property Name of the property containing the username
     *
     * @return string Valid username
     */
    public function getUsername(string $property = 'username')
    {
        try {
            $username = $this->getData($property);

            if (empty($username) || !is_scalar($username)) {
                throw new CannotParseResponse(
                    sprintf('Unable to parse valid %s from service response', $property ?: 'username')
                );
            }

            return $username;
        } catch (CannotParseResponse $e) {
            throw (new ResponseMissingUsername($e->getMessage(), 0, $e))
                ->withDebug([
                    'http_code' => $this->response->getStatusCode(),
                    'content_type' => $this->response->getHeaderLine('Content-Type'),
                    'body' => $this->getBody(),
                    'username' => $username ?? null,
                ]);
        }
    }

    public function getServiceIdentifier()
    {
        return $this->getData('service_identifier');
    }

    public function getPackageIdentifier()
    {
        return $this->getData('package_identifier');
    }
}
