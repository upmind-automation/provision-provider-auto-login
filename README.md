# Upmind Provision Providers - Auto Login

[![Latest Version on Packagist](https://img.shields.io/packagist/v/upmind/provision-provider-auto-login.svg?style=flat-square)](https://packagist.org/packages/upmind/provision-provider-auto-login)

This provision category contains functions to facilitate basic online service account creation/management including an automatic login feature.

- [Installation](#installation)
- [Usage](#usage)
  - [Quick-start](#quick-start)
- [Supported Providers](#supported-providers)
- [Functions](#functions)
  - [create()](#create)
  - [login()](#login)
  - [terminate()](#terminate)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)
- [Upmind](#upmind)

## Installation

```bash
composer require upmind/provision-provider-auto-login
```

## Usage

This library makes use of [upmind/provision-provider-base](https://packagist.org/packages/upmind/provision-provider-base) primitives which we suggest you familiarize yourself with by reading the usage section in the README.

### Quick-start

See the below example to create a SpamExperts account:

```php
<?php

use Illuminate\Support\Facades\App;
use Upmind\ProvisionBase\ProviderFactory;

$factory = App::make(ProviderFactory::class);

$configuration = [
    'dashboard_url' => 'https://my.spamexperts.com',
    'username' => 'example',
    'password' => '{password}',
];
$provider = $factory->create('auto-login', 'spam-experts', $configuration);

$createParameters = [
    'service_identifier' => 'example.com',
    'email' => 'harry@upmind.com',
    'package_name' => 'incoming,outgoing',
];
$function = $provider->makeJob('create', $createParameters);

$result = $function->execute();

if ($result->isError()) {
    throw new RuntimeException($result->getMessage(), 0, $result->getException());
}

/** @var \Upmind\ProvisionProviders\AutoLogin\Data\CreateResult */
$accountInfo = $result->getData();

// $accountInfo->username; // username/identifier of the created hosting account
// ...
```

## Supported Providers

The following providers are currently implemented:
  - SpamExperts
  - Generic (a generic highly configurable provider)

## Functions

### create()

Creates an account and returns the `username` which can be used to identify the account in subsequent requests, plus other account information.

### login()

Obtain a signed login URL for the service that the system client can redirect to.

### terminate()

Delete an account for this service.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

 - [Harry Lewis](https://github.com/uphlewis)
 - [All Contributors](../../contributors)

## License

GNU General Public License version 3 (GPLv3). Please see [License File](LICENSE.md) for more information.

## Upmind

Sell, manage and support web hosting, domain names, ssl certificates, website builders and more with [Upmind.com](https://upmind.com/start) - the ultimate web hosting billing and management solution.