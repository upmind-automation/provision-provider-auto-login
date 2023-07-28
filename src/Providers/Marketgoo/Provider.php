<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Marketgoo;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Upmind\ProvisionProviders\AutoLogin\Category;
use Upmind\ProvisionBase\Provider\Contract\ProviderInterface;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionProviders\AutoLogin\Data\CreateParams;
use Upmind\ProvisionProviders\AutoLogin\Data\CreateResult;
use Upmind\ProvisionProviders\AutoLogin\Data\EmptyResult;
use Upmind\ProvisionProviders\AutoLogin\Data\LoginResult;
use Upmind\ProvisionProviders\AutoLogin\Data\AccountIdentifierParams;
use Upmind\ProvisionProviders\AutoLogin\Providers\Marketgoo\Data\Configuration;
use Upmind\ProvisionProviders\AutoLogin\Providers\Marketgoo\ResponseHandlers\CreateAccountResponseHandler;
use Upmind\ProvisionProviders\AutoLogin\Providers\Marketgoo\ResponseHandlers\ProductListResponseHandler;
use Upmind\ProvisionProviders\AutoLogin\Providers\Marketgoo\ResponseHandlers\ResponseHandler;

class Provider extends Category implements ProviderInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;

    public static function aboutProvider(): AboutData
    {
        return AboutData::create()
            ->setName('marketgoo')
            ->setDescription('Create, login to and delete marketgoo accounts')
            ->setLogoUrl('https://apps.marketgoo.com/assets/branding/marketgoo/logo-squared.png');
    }

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function create(CreateParams $params): CreateResult
    {
        $domainName = $params->service_identifier;
        $productKey = $params->package_identifier;
        $email = $params->email;
        $name = substr($email, 0, strrpos($email, '@'));

        $accountId = $this->createAccount($domainName, $productKey, $email, $name);

        return CreateResult::create()
            ->setUsername($accountId)
            ->setServiceIdentifier($domainName)
            ->setPackageIdentifier($productKey)
            ->setMessage('Account created');
    }

    public function login(AccountIdentifierParams $params): LoginResult
    {
        return LoginResult::create()->setUrl($this->getLoginUrl($params->username));
    }

    public function suspend(AccountIdentifierParams $params): EmptyResult
    {
        $this->suspendAccount($params->username);
        return EmptyResult::create()->setMessage('Account suspended');
    }

    public function unsuspend(AccountIdentifierParams $params): EmptyResult
    {
        $this->resumeAccount($params->username);
        return EmptyResult::create()->setMessage('Account unsuspended');
    }

    public function terminate(AccountIdentifierParams $params): EmptyResult
    {
        $this->deleteAccount($params->username);
        return EmptyResult::create()->setMessage('Account deleted');
    }

    protected function client(): Client
    {
        return new Client([
            'base_uri' => rtrim($this->configuration->internal_domain, '/') . '/api/',
            'handler' => $this->getGuzzleHandlerStack(!!$this->configuration->debug),
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => [
                'Accept' => 'application/vnd.marketgoo.api+json',
                'Content-Type' => 'application/vnd.marketgoo.api+json;charset=utf-8',
                'X-Auth-Token' => $this->configuration->api_key,
            ]
        ]);
    }

    private function createAccount(
        string $domainName,
        string $productKey,
        string $email,
        string $name
    ): string {
        $response = $this->client()->post('accounts', [
            RequestOptions::FORM_PARAMS => [
                'data' => [
                    'type' => 'account',
                    'attributes' => [
                        'domain' => $domainName,
                        'product' => $productKey,
                        'name' => $name,
                        'email' => $email,
                    ],
                ],
            ],
        ]);

        $handler = new CreateAccountResponseHandler($response);
        return $handler->getAccountIdentifier('create');
    }

    protected function getLoginUrl(string $username): string
    {
        return $this->configuration->public_domain . sprintf('/login?uuid=%s', $username);
    }

    private function suspendAccount(string $accountId): void
    {
        $response = $this->client()->patch("accounts/{$accountId}/suspend");
        $handler = new ResponseHandler($response);
        $handler->assertSuccess('suspend');
    }

    private function resumeAccount(string $accountId): void
    {
        $response = $this->client()->patch("accounts/{$accountId}/resume");
        $handler = new ResponseHandler($response);
        $handler->assertSuccess('resume');
    }

    private function deleteAccount(string $accountId): void
    {
        $response = $this->client()->delete("accounts/{$accountId}");
        $handler = new ResponseHandler($response);
        $handler->assertSuccess('delete');
    }
}
