<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Upmind\ProvisionBase\Provider\Contract\ProviderInterface;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionProviders\AutoLogin\Category;
use Upmind\ProvisionProviders\AutoLogin\Data\CreateParams;
use Upmind\ProvisionProviders\AutoLogin\Data\CreateResult;
use Upmind\ProvisionProviders\AutoLogin\Data\LoginParams;
use Upmind\ProvisionProviders\AutoLogin\Data\LoginResult;
use Upmind\ProvisionProviders\AutoLogin\Data\TerminateParams;
use Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\Data\Configuration;
use Upmind\ProvisionProviders\AutoLogin\Providers\Generic\ResponseHandlers\UrlResponseHandler;
use Upmind\ProvisionProviders\AutoLogin\Providers\Generic\ResponseHandlers\UsernameResponseHandler;
use Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\ResponseHandlers\AddDomainResponseHandler;
use Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\ResponseHandlers\AuthTicketResponseHandler;
use Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\ResponseHandlers\GetDomainProductsResponseHandler;
use Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\ResponseHandlers\ProductListResponseHandler;
use Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\ResponseHandlers\RemoveDomainResponseHandler;
use Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\ResponseHandlers\SetDomainProductsResponseHandler;

class Provider extends Category implements ProviderInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;

    public static function aboutProvider(): AboutData
    {
        return AboutData::create()
            ->setName('SpamExperts')
            ->setDescription('Create, login to and delete SpamExperts domains');
    }

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function create(CreateParams $params): CreateResult
    {
        $domainName = $params->service_identifier;
        $package = $params->package_identifier;

        if (empty($package)) {
            // default to all available products
            $package = implode(',', $this->getAvailableProducts());
        }

        $this->createDomain($domainName);
        $this->setDomainProducts($domainName, $package);

        return CreateResult::create()
            ->setUsername($domainName)
            ->setServiceIdentifier($domainName)
            ->setPackageIdentifier(implode(',', $this->getDomainProducts($domainName)));
    }

    public function login(LoginParams $params): LoginResult
    {
        $domainName = $params->service_identifier ?: $params->username;

        return LoginResult::create()
            ->setUrl($this->getLoginUrl($domainName));
    }

    public function terminate(TerminateParams $params): ResultData
    {
        $domainName = $params->service_identifier ?: $params->username;

        $this->removeDomain($domainName);

        return ResultData::create();
    }

    protected function createDomain(string $domain): void
    {
        $response = $this->client()->post(sprintf('domain/add/domain/%s', $domain));
        $handler = new AddDomainResponseHandler($response);
        $handler->assertSuccess();
    }

    protected function removeDomain(string $domain): void
    {
        $response = $this->client()->post(sprintf('domain/remove/domain/%s', $domain));
        $handler = new RemoveDomainResponseHandler($response);
        $handler->assertSuccess();
    }

    /**
     * @return string[]
     */
    protected function getAvailableProducts(): array
    {
        $response = $this->client()->post('productslist/get/getavailable/1');
        $handler = new ProductListResponseHandler($response);

        return $handler->getProducts();
    }

    /**
     * @return string[]
     */
    protected function getDomainProducts(string $domain): array
    {
        $response = $this->client()->post(sprintf('domain/getproducts/domain/%s', $domain));
        $handler = new GetDomainProductsResponseHandler($response);

        return $handler->getProducts();
    }

    /**
     * Set the enabled products on a domain.
     *
     * @param string $domain
     * @param string|string[] $products Array or CSV of products to sync this domain to
     */
    protected function setDomainProducts(string $domain, $package): void
    {
        if (is_array($package)) {
            $package = implode(',', $package);
        }
        $package = strtolower($package);

        $allProducts = [
            'incoming',
            'outgoing',
            'archiving'
        ];
        $setProducts = [];
        foreach ($allProducts as $product) {
            $setProducts[] = $product . '/' . intval(Str::contains($package, $product));
        }

        $response = $this->client()->post(
            sprintf('domain/setproducts/domain/%s/%s', $domain, implode('/', $setProducts))
        );
        $handler = new SetDomainProductsResponseHandler($response);
        $handler->assertSuccess();
    }

    /**
     * @param string $username Username or domain name
     *
     * @return string Login url
     */
    protected function getLoginUrl(string $username): string
    {
        $response = $this->client()->post(sprintf('authticket/create/username/%s', $username));
        $handler = new AuthTicketResponseHandler($response);

        return $this->configuration->dashboard_url . sprintf('?authticket=%s', $handler->getTicket());
    }

    protected function client(): Client
    {
        return new Client([
            'base_uri' => rtrim($this->configuration->dashboard_url, '/') . '/api/',
            RequestOptions::AUTH => [ // basic auth
                $this->configuration->username,
                $this->configuration->password
            ],
            RequestOptions::HTTP_ERRORS => false,
            'handler' => $this->getGuzzleHandlerStack(!!$this->configuration->debug)
        ]);
    }
}
