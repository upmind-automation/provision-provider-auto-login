<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Marketgoo\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $public_domain URL of the marketgoo public facing API domain
 * @property-read string $internal_domain URL of the marketgoo provision internal API domain
 * @property-read string $api_key marketgoo API KEY for provision API
 * @property-read bool|null $debug Whether or not to log all HTTP requests and responses
 */
class Configuration extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'public_domain' => ['required', 'url', 'starts_with:https'],
            'internal_domain' => ['required', 'url', 'starts_with:https'],
            'api_key' => ['required', 'string'],
            'debug' => ['boolean'],
        ]);
    }
}
