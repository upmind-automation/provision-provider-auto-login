<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\SpamExperts\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $dashboard_url URL of the SpamExperts dashboard
 * @property-read string $username Admin username
 * @property-read string $password Admin password
 * @property-read bool|null $debug Whether or not to log all HTTP requests and responses
 */
class Configuration extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'dashboard_url' => ['required', 'url', /* 'starts_with:https' */],
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'debug' => ['boolean'],
        ]);
    }
}
