<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Generic;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Upmind\ProvisionBase\Provider\Contract\ProviderInterface;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionProviders\AutoLogin\Category;
use Upmind\ProvisionProviders\AutoLogin\Data\CreateParams;
use Upmind\ProvisionProviders\AutoLogin\Data\CreateResult;
use Upmind\ProvisionProviders\AutoLogin\Data\LoginParams;
use Upmind\ProvisionProviders\AutoLogin\Data\LoginResult;
use Upmind\ProvisionProviders\AutoLogin\Data\TerminateParams;
use Upmind\ProvisionProviders\AutoLogin\Providers\Generic\Data\Configuration;
use Upmind\ProvisionProviders\AutoLogin\Providers\Generic\ResponseHandlers\TerminateResponseHandler;
use Upmind\ProvisionProviders\AutoLogin\Providers\Generic\ResponseHandlers\UrlResponseHandler;
use Upmind\ProvisionProviders\AutoLogin\Providers\Generic\ResponseHandlers\UsernameResponseHandler;

class Provider extends Category implements ProviderInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;

    public static function aboutProvider(): AboutData
    {
        return AboutData::create()
            ->setName('Generic Login')
        ->setDescription(
            'A highly-configurable generic auto login provider for services which use bearer token auth'
        );
    }

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function login(LoginParams $params): LoginResult
    {
        $method = strtoupper($this->configuration->login_endpoint_http_method);
        $endpointUrl = $this->configuration->login_endpoint_url;

        $options = [];

        if ($this->configuration->access_token) {
            $options[RequestOptions::HEADERS] = [
                'Authorization' => sprintf('Bearer %s', $this->configuration->access_token),
            ];
        }

        if ($method === 'GET') {
            $options[RequestOptions::QUERY] = $params->toArray();
        } else {
            $options[RequestOptions::FORM_PARAMS] = $params->toArray();
        }

        $response = $this->client()->request($method, $endpointUrl, $options);
        $handler = new UrlResponseHandler($response);

        return LoginResult::create()
            ->setUrl($handler->getUrl(null));
    }

    public function create(CreateParams $params): CreateResult
    {
        if (!$this->configuration->has_create) {
            return $this->errorResult('No create endpoint set in this configuration');
        }

        $method = strtoupper($this->configuration->create_endpoint_http_method);
        $endpointUrl = $this->configuration->create_endpoint_url;

        $requestParams = $params->toArray();
        $requestParams = array_merge($requestParams, Arr::pull($requestParams, 'extra', [])); // merge extra params

        $options = [];

        if ($this->configuration->access_token) {
            $options[RequestOptions::HEADERS] = [
                'Authorization' => sprintf('Bearer %s', $this->configuration->access_token),
            ];
        }

        if ($method === 'GET') {
            $options[RequestOptions::QUERY] = $requestParams;
        } else {
            $options[RequestOptions::FORM_PARAMS] = $requestParams;
        }

        $response = $this->client()->request($method, $endpointUrl, $options);
        $handler = new UsernameResponseHandler($response);

        return CreateResult::create()
            ->setUsername($handler->getUsername())
            ->setServiceIdentifier($handler->getServiceIdentifier() ?? $params->service_identifier)
            ->setPackageIdentifier($handler->getPackageIdentifier() ?? $params->package_identifier);
    }

    public function terminate(TerminateParams $params): ResultData
    {
        if (!$this->configuration->has_terminate) {
            return $this->errorResult('No terminate endpoint set in this configuration');
        }

        $method = strtoupper($this->configuration->terminate_endpoint_http_method);
        $endpointUrl = $this->configuration->terminate_endpoint_url;

        $requestParams = $params->toArray();
        $requestParams = array_merge($requestParams, Arr::pull($requestParams, 'extra', [])); // merge extra params

        $options = [];

        if ($this->configuration->access_token) {
            $options[RequestOptions::HEADERS] = [
                'Authorization' => sprintf('Bearer %s', $this->configuration->access_token),
            ];
        }

        if ($method === 'GET') {
            $options[RequestOptions::QUERY] = $requestParams;
        } else {
            $options[RequestOptions::FORM_PARAMS] = $requestParams;
        }

        $response = $this->client()->request($method, $endpointUrl, $options);
        $handler = new TerminateResponseHandler($response);
        $handler->assertSuccess();

        return ResultData::create();
    }

    protected function client(): Client
    {
        return new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);
    }
}
