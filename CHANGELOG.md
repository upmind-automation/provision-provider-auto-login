# Changelog

All notable changes to the package will be documented in this file.

## [v5.1.2](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v5.1.2) - 2023-11-08

- Update ResponseHandlers/AbstractHandler::assertSuccess() to attempt to extract an error message

## [v5.1.1](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v5.1.1) - 2023-11-08

- Fix Generic/Provider::login() return URL property

## [v5.1](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v5.1) - 2023-11-03

- Update AccountIdentifierParams add optional package_identifier

## [v5.0](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v5.0) - 2023-09-19

- Move Marketgoo provider to [upmind/provision-provider-seo](https://github.com/upmind-automation/provision-provider-seo)

## [v4.0](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v4.0) - 2023-09-13

- Refactor Category function parameter + return datasets
  - Delete LoginParams and TerminateParams in favour of AccountIdentifierParams with optional `extra` array
  - Replace usage of ResultData with EmptyResult
- Add `suspend()` and `unsuspend()` functions
- Add Example provider
- Add optional `customer_name` and `promo_codes` to CreateParams
- Add marketgoo provider

## [v3.0.3](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v3.0.3) - 2022-12-02

Add Category icon and SpamExperts Provider logo

## [v3.0.2](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v3.0.2) - 2022-12-02

Add optional HTTP request/response debug logging to Generic and SpamExperts providers

## [v3.0.1](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v3.0.1) - 2022-10-15

Enable compatibility with upmind/provision-provider-base v3

## [v3.0](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v3.0) - 2022-04-29

Initial public release
