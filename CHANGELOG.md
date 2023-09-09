# Changelog

All notable changes to the package will be documented in this file.

## v4.0 - TBC

- Refactor Category function parameter + return datasets
  - Delete LoginParams and TerminateParams in favour of AccountIdentifierParams with optional `extra` array
  - Replace usage of ResultData with EmptyResult
- Add `suspend()` and `unsuspend()` functions
- Add Example provider
- Add optional `customer_name` and `promo_codes` to CreateParams

## [v3.0.3](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v3.0.3) - 2022-12-02

Add Category icon and SpamExperts Provider logo

## [v3.0.2](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v3.0.2) - 2022-12-02

Add optional HTTP request/response debug logging to Generic and SpamExperts providers

## [v3.0.1](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v3.0.1) - 2022-10-15

Enable compatibility with upmind/provision-provider-base v3

## [v3.0](https://github.com/upmind-automation/provision-provider-auto-login/releases/tag/v3.0) - 2022-04-29

Initial public release
