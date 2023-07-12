<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin;

use Upmind\ProvisionBase\Provider\BaseCategory;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionProviders\AutoLogin\Data\AccountIdentifierParams;
use Upmind\ProvisionProviders\AutoLogin\Data\CreateParams;
use Upmind\ProvisionProviders\AutoLogin\Data\CreateResult;
use Upmind\ProvisionProviders\AutoLogin\Data\LoginResult;

/**
 * This provision category contains functions to facilitate basic online service
 * account creation/management including an automatic login feature.
 */
abstract class Category extends BaseCategory
{
    public static function aboutCategory(): AboutData
    {
        return AboutData::create()
            ->setName('Auto Login')
            ->setDescription('Basic provision category which provides auto-login to compatible services')
            ->setIcon('key');
    }

    /**
     * Creates an account and returns the `username` which can be used to
     * identify the account in subsequent requests, plus other account
     * information.
     */
    abstract public function create(CreateParams $params): CreateResult;

    /**
     * Obtain a signed login URL for the service that the system client can redirect to.
     */
    abstract public function login(AccountIdentifierParams $params): LoginResult;

    /**
     * Suspend an account for this service.
     */
    abstract public function suspend(AccountIdentifierParams $params): ResultData;

    /**
     * Unsuspend an account for this service.
     */
    abstract public function unsuspend(AccountIdentifierParams $params): ResultData;

    /**
     * Permanently delete an account for this service.
     */
    abstract public function terminate(AccountIdentifierParams $params): ResultData;
}
