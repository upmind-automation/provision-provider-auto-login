<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Example;

use GuzzleHttp\Client;
use Upmind\ProvisionBase\Provider\Contract\ProviderInterface;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionProviders\AutoLogin\Category;
use Upmind\ProvisionProviders\AutoLogin\Data\CreateParams;
use Upmind\ProvisionProviders\AutoLogin\Data\CreateResult;
use Upmind\ProvisionProviders\AutoLogin\Data\LoginParams;
use Upmind\ProvisionProviders\AutoLogin\Data\LoginResult;
use Upmind\ProvisionProviders\AutoLogin\Data\TerminateParams;

/**
 * Empty provider for demonstration purposes.
 */
class Provider extends Category implements ProviderInterface
{
    protected Configuration $configuration;
    protected Client $client;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @inheritDoc
     */
    public static function aboutProvider(): AboutData
    {
        return AboutData::create()
            ->setName('Example Provider')
            // ->setLogoUrl('https://example.com/logo.png')
            ->setDescription('Empty provider for demonstration purposes');
    }

    /**
     * @inheritDoc
     */
    public function create(CreateParams $params): CreateResult
    {
        throw $this->errorResult('Not Implemented');
    }

    /**
     * Obtain a signed login URL for the service that the system client can redirect to.
     */
    public function login(LoginParams $params): LoginResult
    {
        // $this->apiCall();

        return LoginResult::create()
            ->setMessage('Login URL generated')
            ->setUrl('https://example.com/login/foo/?auth=xxxxxx');
    }

    /**
     * Delete an account for this service.
     */
    public function terminate(TerminateParams $params): ResultData
    {
        throw $this->errorResult('Not Implemented');
    }

    /**
     * Get a Guzzle HTTP client instance.
     */
    protected function client(): Client
    {
        return $this->client ??= new Client([
            'handler' => $this->getGuzzleHandlerStack(boolval($this->configuration->debug)),
            'base_uri' => 'https://example.com/api/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->configuration->api_token,
            ],
        ]);
    }
}
