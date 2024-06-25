<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $url Signed login url
 */
class LoginResult extends ResultData
{
    public static function rules(): Rules
    {
        return new Rules([
            'url' => ['required', 'url'],
        ]);
    }

    /**
     * Set the result URL.
     */
    public function setUrl(string $url): self
    {
        $this->setValue('url', $url);
        return $this;
    }
}
