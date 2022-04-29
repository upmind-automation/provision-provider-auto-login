<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Generic\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string|null $access_token Access bearer token to send in requests
 * @property-read string $login_endpoint_url Endpoint which generates login URLs
 * @property-read string|null $login_endpoint_http_method HTTP method to use for the login endpoint
 * @property-read boolean $has_create Whether or not this configuration has a create endpoint
 * @property-read string|null $create_endpoint_url Endpoint which creates a service account and returns a username
 * @property-read string|null $create_endpoint_http_method HTTP method to use for the create endpoint
 * @property-read boolean $has_terminate Whether or not this configuration has a terminate endpoint
 * @property-read string|null $terminate_endpoint_url Endpoint which terminates a service account
 * @property-read string|null $terminate_endpoint_http_method HTTP method to use for the terminate endpoint
 */
class Configuration extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'access_token' => ['nullable', 'string'],
            'login_endpoint_url' => ['required', 'url', /* 'starts_with:https' */],
            'login_endpoint_http_method' => ['required', 'string', 'in:post,put,patch,get'],
            'has_create' => ['boolean'],
            'create_endpoint_url' => ['required_if:has_create,1', 'url', /* 'starts_with:https' */],
            'create_endpoint_http_method' => ['required_if:has_create,1', 'string', 'in:post,put,patch,get'],
            'has_terminate' => ['boolean'],
            'terminate_endpoint_url' => ['required_if:has_terminate,1', 'url', /* 'starts_with:https' */],
            'terminate_endpoint_http_method' => ['required_if:has_terminate,1', 'string', 'in:post,put,patch,get,delete'],
        ]);
    }
}
