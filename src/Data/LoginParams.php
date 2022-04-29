<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read mixed $username Username or other unique service identifier
 * @property-read string|null $service_identifier Secondary service identifier, if any
 */
class LoginParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'username' => ['required'],
            'service_identifier' => ['nullable', 'string'],
        ]);
    }
}
