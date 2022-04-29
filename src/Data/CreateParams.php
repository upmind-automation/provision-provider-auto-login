<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read mixed $user_id Id of the user
 * @property-read string|null $service_identifier Secondary service identifier to use, if known up-front
 * @property-read string|null $package_identifier Service package identifier, if any
 * @property-read string|null $email Email address of the user
 * @property-read mixed[]|null $extra Any extra data to pass to the service endpoint
 */
class CreateParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'user_id' => ['required'],
            'service_identifier' => ['nullable', 'string'],
            'package_identifier' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'extra' => ['nullable', 'array'],
        ]);
    }
}
