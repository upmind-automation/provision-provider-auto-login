<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Successful result with no data.
 */
class EmptyResult extends ResultData
{
    public static function rules(): Rules
    {
        return new Rules([]);
    }
}
